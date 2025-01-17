<?php

namespace App\Imports;

use App\Models\Customer;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class CustomersImport implements ToModel, WithHeadingRow, WithValidation
{
    private $groupId;

    public function __construct($groupId = null)
    {
        $this->groupId = $groupId;
    }

    public function model(array $row)
    {
        return new Customer([
            'name' => $row['name'],
            'mobile' => $row['mobile'],
            'email' => $row['email'] ?? null,
            'group_id' => $this->groupId,
            'status' => $row['status'] ?? true,
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'mobile' => 'required|string|unique:customers,mobile',
            'email' => 'nullable|email|unique:customers,email',
        ];
    }
}
