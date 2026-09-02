<?php

namespace App\Console\Commands;

use App\Models\InventoryPhoto;
use App\Models\Order;
use App\Models\OrderAttachment;
use App\Models\Payment;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Throwable;

#[Signature('media:migrate-legacy-private {--delete-source : Delete public sources after verifying their private copies}')]
#[Description('Copy referenced legacy media from public storage to private storage')]
class MigrateLegacyPrivateMedia extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $public = Storage::disk('public');
        $private = Storage::disk('local');
        $counts = ['copied' => 0, 'verified' => 0, 'deleted' => 0, 'missing' => 0, 'conflicts' => 0, 'imported' => 0];

        if (Schema::hasTable('order_attachments')) {
            $this->importLegacyOrderPhotos($public, $counts);
        }

        $this->migrateModels(Payment::query()->whereNotNull('proof_path')->cursor(), 'proof_path', $public, $private, $counts);
        $this->migrateModels(InventoryPhoto::query()->whereNotNull('file_path')->cursor(), 'file_path', $public, $private, $counts);

        if (Schema::hasTable('order_attachments')) {
            $this->migrateModels(OrderAttachment::query()->whereNotNull('file_path')->cursor(), 'file_path', $public, $private, $counts);
        }

        $this->table(array_keys($counts), [array_values($counts)]);

        return ($counts['missing'] + $counts['conflicts']) > 0 ? self::FAILURE : self::SUCCESS;
    }

    /**
     * @param  iterable<Model>  $models
     * @param  array<string, int>  $counts
     */
    private function migrateModels(iterable $models, string $pathAttribute, FilesystemAdapter $public, FilesystemAdapter $private, array &$counts): void
    {
        foreach ($models as $model) {
            $path = $model->getAttribute($pathAttribute);

            if (is_string($path) && $path !== '') {
                $this->migrateFile($path, $public, $private, $counts, $model::class.'#'.$model->getKey());
            }
        }
    }

    /** @param array<string, int> $counts */
    private function migrateFile(string $path, FilesystemAdapter $public, FilesystemAdapter $private, array &$counts, string $owner): void
    {
        $hasPublic = $public->exists($path);

        if ($private->exists($path)) {
            if ($hasPublic && $private->size($path) !== $public->size($path)) {
                $counts['conflicts']++;
                $this->error("Conflicting private file for {$owner}: {$path}");

                return;
            }

            $counts['verified']++;
        } elseif (! $hasPublic) {
            $counts['missing']++;
            $this->warn("Missing file for {$owner}: {$path}");

            return;
        } else {
            try {
                $stream = $public->readStream($path);

                if ($stream === null || $stream === false) {
                    throw new RuntimeException('Unable to open source stream.');
                }

                try {
                    $private->writeStream($path, $stream);
                } finally {
                    if (is_resource($stream)) {
                        fclose($stream);
                    }
                }

                if (! $private->exists($path) || $private->size($path) !== $public->size($path)) {
                    $private->delete($path);
                    throw new RuntimeException('Private copy did not match the source size.');
                }

                $counts['copied']++;
            } catch (Throwable $exception) {
                $counts['conflicts']++;
                $this->error("Failed to copy {$owner} ({$path}): {$exception->getMessage()}");

                return;
            }
        }

        if ($this->option('delete-source') && $hasPublic && $public->delete($path)) {
            $counts['deleted']++;
        }
    }

    /** @param array<string, int> $counts */
    private function importLegacyOrderPhotos(FilesystemAdapter $public, array &$counts): void
    {
        foreach ($public->allFiles('orders') as $path) {
            if (! preg_match('#^orders/(\d+)/estimation/([^/]+)$#', $path, $matches)) {
                continue;
            }

            $order = Order::query()->find((int) $matches[1]);
            if (! $order) {
                $this->warn("Orphaned legacy order photo: {$path}");

                continue;
            }

            $attachment = OrderAttachment::query()->firstOrCreate(
                ['order_id' => $order->id, 'file_path' => $path],
                [
                    'type' => 'ESTIMATION_PHOTO',
                    'original_name' => basename($path),
                    'mime_type' => $public->mimeType($path),
                    'file_size' => $public->size($path),
                ],
            );

            if ($attachment->wasRecentlyCreated) {
                $counts['imported']++;
            }
        }
    }
}
