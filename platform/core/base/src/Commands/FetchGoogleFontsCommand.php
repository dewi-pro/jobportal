<?php

namespace Botble\Base\Commands;

use Botble\Base\Commands\Traits\ValidateCommandInput;
use Exception;
use Illuminate\Console\Command;

use function Laravel\Prompts\text;

use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand('cms:google-fonts:fetch', 'Fetch Google Fonts and store them on a local disk')]
class FetchGoogleFontsCommand extends Command
{
    use ValidateCommandInput;

    public function handle(): int
    {
        $font = text(
            label: 'Google Font URL',
            required: true,
            validate: $this->validate('url|starts_with:https://fonts.googleapis.com/css2?family='),
        );

        $this->components->info(sprintf('Fetching <comment>%s</comment>...', $font));

        try {
            app('core.google-fonts')->load($font, forceDownload: true);
        } catch (Exception $exception) {
            $this->components->error($exception->getMessage());

            return self::FAILURE;
        }

        $this->components->info('All done!');

        return self::SUCCESS;
    }
}
