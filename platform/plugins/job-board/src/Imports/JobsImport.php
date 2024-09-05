<?php

namespace Botble\JobBoard\Imports;

use Botble\Base\Events\CreatedContentEvent;
use Botble\JobBoard\Contracts\OnSuccesses;
use Botble\JobBoard\Contracts\Typeable;
use Botble\JobBoard\Contracts\Validatable;
use Botble\JobBoard\Facades\JobBoardHelper;
use Botble\JobBoard\Models\CareerLevel;
use Botble\JobBoard\Models\Category;
use Botble\JobBoard\Models\Company;
use Botble\JobBoard\Models\Currency;
use Botble\JobBoard\Models\DegreeLevel;
use Botble\JobBoard\Models\FunctionalArea;
use Botble\JobBoard\Models\Job;
use Botble\JobBoard\Models\JobExperience;
use Botble\JobBoard\Models\JobShift;
use Botble\JobBoard\Models\JobSkill;
use Botble\JobBoard\Models\JobType;
use Botble\JobBoard\Models\Tag;
use Botble\Location\Models\City;
use Botble\Location\Models\Country;
use Botble\Location\Models\State;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithValidation;

class JobsImport implements
    ToModel,
    WithValidation,
    WithChunkReading,
    WithHeadingRow,
    WithMapping
{
    use Importable;
    use SkipsFailures;
    use SkipsErrors;
    use Validatable;
    use OnSuccesses;
    use Typeable;

    public function __construct(protected Request $request)
    {
    }

    public function model(array $row): Model
    {
        $job = new Job();
        $job->forceFill(Arr::except($row, ['skills', 'categories', 'types', 'tags']));
        $job->save();

        $job->skills()->sync(Arr::get($row, 'skills', []));
        $job->categories()->sync(Arr::get($row, 'categories', []));
        $job->jobTypes()->sync(Arr::get($row, 'types', []));
        $job->tags()->sync(Arr::get($row, 'tags', []));

        $job->author()->associate(auth()->user());

        $this->request->merge([
            'slug' => Str::slug($job->name),
            'is_slug_editable' => true,
        ]);

        event(new CreatedContentEvent(JOB_MODULE_SCREEN_NAME, $this->request, $job));

        $this->onSuccess($job);

        return $job;
    }

    public function chunkSize(): int
    {
        return 100;
    }

    public function map($row): array
    {
        $job = [
            'name' => Arr::get($row, 'name'),
            'description' => Arr::get($row, 'description'),
            'content' => Arr::get($row, 'content'),
            'apply_url' => Arr::get($row, 'apply_url'),
            'address' => Arr::get($row, 'address'),
            'is_freelance' => $this->yesNoToBoolean(Arr::get($row, 'is_freelance', false)),
            'salary_from' => Arr::get($row, 'salary_from'),
            'salary_to' => Arr::get($row, 'salary_to'),
            'salary_range' => Arr::get($row, 'salary_range'),
            'hide_salary' => $this->yesNoToBoolean(Arr::get($row, 'hide_salary', false)),
            'number_of_positions' => Arr::get($row, 'number_of_positions', 0),
            'expire_date' => Arr::get($row, 'expire_date'),
            'hide_company' => $this->yesNoToBoolean(Arr::get($row, 'hide_company', false)),
            'latitude' => Arr::get($row, 'latitude'),
            'longitude' => Arr::get($row, 'longitude'),
            'is_featured' => $this->yesNoToBoolean(Arr::get($row, 'is_featured', false)),
            'auto_renew' => $this->yesNoToBoolean(Arr::get($row, 'auto_renew', false)),
            'never_expired' => ! Arr::get($row, 'expire_date') || $this->yesNoToBoolean(Arr::get($row, 'never_expired')),
            'employer_colleagues' => $this->stringToArray(Arr::get($row, 'employer_colleagues')),
            'start_date' => Arr::get($row, 'start_date'),
            'application_closing_date' => Arr::get($row, 'application_closing_date'),
            'status' => Arr::get($row, 'status'),
            'moderation_status' => Arr::get($row, 'moderation_status'),
        ];

        if (JobBoardHelper::isZipCodeEnabled()) {
            $job = array_merge($job, [
                'zip_code' => Arr::get($row, 'zip_code'),
            ]);
        }

        return $this->mapRelationships($row, $job);
    }

    public function mapRelationships(mixed $row, array $job): array
    {
        $job['country_id'] = Arr::first($this->stringToModelIds(Arr::get($row, 'country'), new Country()));
        $job['state_id'] = Arr::first($this->stringToModelIds(Arr::get($row, 'state'), new State()));
        $job['city_id'] = Arr::first($this->stringToModelIds(Arr::get($row, 'city'), new City()));
        $job['currency_id'] = Arr::first($this->stringToModelIds(Arr::get($row, 'currency'), new Currency(), 'title'));
        $job['company_id'] = Arr::first($this->stringToModelIds(Arr::get($row, 'company_name'), new Company()));
        $job['career_level_id'] = Arr::first($this->stringToModelIds(Arr::get($row, 'career_level'), new CareerLevel()));
        $job['degree_level_id'] = Arr::first($this->stringToModelIds(Arr::get($row, 'degree_level'), new DegreeLevel()));
        $job['job_shift_id'] = Arr::first($this->stringToModelIds(Arr::get($row, 'job_shift'), new JobShift()));
        $job['job_experience_id'] = Arr::first($this->stringToModelIds(Arr::get($row, 'job_experience'), new JobExperience()));
        $job['functional_area_id'] = Arr::first($this->stringToModelIds(Arr::get($row, 'functional_area'), new FunctionalArea()));
        $job['skills'] = $this->stringToModelIds(Arr::get($row, 'skills'), new JobSkill());
        $job['categories'] = $this->stringToModelIds(Arr::get($row, 'categories'), new Category());
        $job['types'] = $this->stringToModelIds(Arr::get($row, 'types'), new JobType());
        $job['tags'] = $this->stringToModelIds(Arr::get($row, 'tags'), new Tag());

        return $job;
    }
}
