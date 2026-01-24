<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        // القيم الافتراضية عند تشغيل المشروع لأول مرة
        $this->migrator->add('general.site_name', 'Ahgzly Online');
        $this->migrator->add('general.support_email', 'admin@ahgzly.com');
        $this->migrator->add('general.maintenance_mode', false);
    }
};
