<?php

namespace InetStudio\CustomFieldsPackage\Console\Commands;

use InetStudio\AdminPanel\Base\Console\Commands\BaseSetupCommand;

/**
 * Class SetupCommand.
 */
class SetupCommand extends BaseSetupCommand
{
    /**
     * Имя команды.
     *
     * @var string
     */
    protected $name = 'inetstudio:custom-fields-package:setup';

    /**
     * Описание команды.
     *
     * @var string
     */
    protected $description = 'Setup custom fields package';

    /**
     * Инициализация команд.
     */
    protected function initCommands(): void
    {
        $this->calls = [
            [
                'type' => 'artisan',
                'description' => 'Statuses setup',
                'command' => 'inetstudio:custom-fields-package:fields:setup',
            ],
        ];
    }
}
