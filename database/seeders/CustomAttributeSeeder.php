<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CustomAttributeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
     public function run()
    {
        $attributeRepository = app(AttributeRepository::class);

        // Check if attribute already exists
        if (! $attributeRepository->findWhere(['code' => 'New'])->count()) {
            $attributeRepository->create([
                'code'               => 'ewrer',
                'admin_name'        => 'New',
                'type'              => 'text', // or 'text', 'select' depending on your use
                'is_required'       => 0,
                'is_unique'         => 0,
                'value_per_locale'  => 1,
                'value_per_channel' => 1,
                'is_filterable'     => 1,
                'is_configurable'   => 0,
                'is_user_defined'   => 1,
                'use_in_flat'       => 1,
            ]);
        }
    }
}
