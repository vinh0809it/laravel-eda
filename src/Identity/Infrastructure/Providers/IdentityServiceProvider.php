<?php

declare(strict_types=1);

namespace Src\Identity\Infrastructure\Providers;

use Src\Shared\Infrastructure\Providers\ModuleServiceProvider;

/**
 * Identity: users, API tokens and the Breeze web authentication screens.
 */
final class IdentityServiceProvider extends ModuleServiceProvider
{
    protected ?string $apiRoutes = __DIR__ . '/../../Presentation/routes/api.php';

    protected ?string $webRoutes = __DIR__ . '/../../Presentation/routes/web.php';

    protected ?string $migrations = __DIR__ . '/../Migrations';
}
