<?php
namespace App\Services;

use App\NavbarItem;
use App\Settings\AppSettings;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;

readonly class NavbarService
{
    public function __construct(
        private AppSettings $settings,
    ) {
    }

    public function getItems(): array
    {
        /** @var NavbarItem[] $navbar */
        $navbar = [];

        $routes = Route::getRoutes();

        $items = $this->settings->navbar_items;
        foreach ($items as $routeName => $details) {
            if (str_starts_with($routeName, 'dropdown')) {
                $navbar[$routeName] = new NavbarItem($details);
                continue;
            }

            $title = explode('/', $details['label'], 2);
            if (count($title) === 2) {
                [$dropdown, $title] = $title;
            } else {
                $dropdown = false;
                $title = $title[0];
            }

            if ($routes->getByName($routeName)) {
                if ($this->canAccessRoute($routeName)) {
                    $item = new NavbarItem(['label' => $title, 'order' => $details['order']], $routeName);
                    if ($dropdown) {
                        $navbar[$dropdown]->addChild($item);
                    } else {
                        $navbar[$routeName] = $item;
                    }
                }
            } elseif (Gate::allows('edit_config')) {
                request()->session()->now('error', 'The navigation menu config contains an invalid route (' . $routeName . '). You should fix this ASAP.');
            }
        }

        return $navbar;
    }

    public function canAccessRoute($routeName): bool
    {
        $route = Route::getRoutes()->getByName($routeName);
        if (!$route) {
            return false;
        }

        $ability = collect($route->middleware())
            ->filter(fn ($m) => str_starts_with($m, 'can:'))
            ->map(fn ($m) => explode(':', $m, 2)[1])
            ->first();

        // If there's no 'can' middleware, there are no access restrictions.
        if (!$ability) {
            return true;
        }

        /**
         * @TODO: This is derived from the same nastiness as the Gate check in AppServiceProvider.
         */
        if (str_starts_with($ability, 'conditionally_public')) {
            if (!$routeName) {
                return false;
            }

            if ($this->settings->isPagePublic($routeName)) {
                return true;
            }

            [, $actualAbility] = explode('|', $ability, 2);
            return Auth::user()?->canDo($actualAbility) ?? false;
        }

        return Gate::any($ability);
    }
}
