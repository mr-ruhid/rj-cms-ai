<?php

namespace App\Traits;

use App\Models\Revision;

trait HasRevisions
{
    public function revisions()
    {
        return $this->morphMany(Revision::class, 'revisionable')->orderByDesc('version');
    }

    public function latestRevision()
    {
        return $this->morphOne(Revision::class, 'revisionable')->latestOfMany('version');
    }

    public function createRevision(?string $note = null): ?Revision
    {
        $content = $this->revisionContent();

        if (empty($content)) {
            return null;
        }

        $revision = Revision::createFor($this, $content, [
            'note' => $note,
            'title' => $this->revisionTitle(),
        ]);

        $this->pruneRevisions();

        return $revision;
    }

    public function restoreRevision(int $revisionId): bool
    {
        $revision = $this->revisions()->find($revisionId);

        if (!$revision) {
            return false;
        }

        $this->restoreFromRevision($revision->content);

        $this->save();

        return true;
    }

    protected function revisionContent(): array
    {
        $fields = property_exists($this, 'revisionable')
            ? $this->revisionable
            : ['content', 'settings', 'conditions'];

        $content = [];

        foreach ($fields as $field) {
            if (isset($this->{$field})) {
                $content[$field] = $this->{$field};
            }
        }

        return $content;
    }

    protected function revisionTitle(): string
    {
        foreach (['name', 'title'] as $field) {
            if (!empty($this->{$field})) {
                return (string) $this->{$field};
            }
        }

        return '#' . $this->id;
    }

    protected function restoreFromRevision(array $content): void
    {
        foreach ($content as $field => $value) {
            $this->{$field} = $value;
        }
    }

    protected function pruneRevisions(): void
    {
        $keep = property_exists($this, 'revisionLimit') ? (int) $this->revisionLimit : 20;

        Revision::pruneFor($this, $keep);
    }
}