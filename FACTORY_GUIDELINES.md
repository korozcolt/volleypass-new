# Factory Guidelines

## Important: Geographic Data Factories

**DO NOT CREATE** the following factories:
- CityFactory
- DepartmentFactory  
- CountryFactory

### Reason
All geographic information (countries, departments, cities) is managed through database seeders, not factories. This data represents real geographic locations and should be consistent across all environments.

### Usage
If you need geographic data in tests or development:
1. Run the geographic data seeder: `php artisan db:seed --class=GeographicDataSeeder`
2. Use existing records from the database
3. Reference existing IDs in your factories and tests

### Example
Instead of:
```php
'city_id' => City::factory(),
'department_id' => Department::factory(),
```

Use:
```php
'city_id' => City::first()?->id ?? 1,
'department_id' => Department::first()?->id ?? 1,
```

Or create specific test data in your test setup methods using existing seeded data.