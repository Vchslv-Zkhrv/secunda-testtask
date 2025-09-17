<?php

namespace App\Services;

use App\Models\Company;
use Illuminate\Database\Eloquent\Collection;

class CompanyService
{
    /**
     * @return Collection<int,Company>
     */
    public function searchCompanies(string $search): Collection
    {
        $search = trim($search);
        $search = preg_replace("/\s+/", "%", $search);
        $search = "%$search%";

        return Company::query()->select()->whereLike('name', $search)->get();
    }
}
