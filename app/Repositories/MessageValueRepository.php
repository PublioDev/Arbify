<?php

declare(strict_types=1);

namespace Arbify\Repositories;

use Arbify\Contracts\Repositories\MessageValueRepository as MessageValueRepositoryContract;
use Arbify\Models\Language;
use Arbify\Models\Message;
use Arbify\Models\MessageValue;
use Arbify\Models\Project;
use Arr;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Collection;

class MessageValueRepository implements MessageValueRepositoryContract
{
    public function byMessageLanguageAndFormOrCreate(Message $message, Language $language, ?string $form): MessageValue
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $message->messageValues()
            ->where('language_id', $language->id)
            ->where('form', $form)
            ->firstOrCreate([
                'language_id' => $language->id,
                'form' => $form,
            ]);
    }

    public function allByProjectAssociativeGrouped(Project $project): array
    {
        $values = $project->messageValues()->get([
            'message_id',
            'language_id',
            'form',
            'name',
            'value',
        ])->toArray();

        $results = [];
        foreach ($values as $value) {
            $results[$value['message_id']][$value['language_id']][$value['form']] = $value;
        }

        return $results;
    }

    public function allByProjectAndLanguage(Project $project, Language $language): Collection
    {
        return $project->messageValues()
            ->where('language_id', $language->id)
            ->get();
    }

    public function languageGroupedDetailsByProject(Project $project): array
    {
        // FIXME: Maybe replace this with a query without the n+1 problem.
        $result = $project->languages
            ->map(function (Language $language) use ($project) {
                /** @var string $lastModified */
                $lastModified = $project->messageValues()
                    ->where('language_id', $language->id)
                    ->max('message_values.updated_at');

                $lastModifiedIso8601 = $lastModified ? Carbon::parse($lastModified)->toIso8601String() : null;

                return [$language->code => $lastModifiedIso8601];
            });

        return Arr::collapse($result);
    }
}
