<?php
namespace App\Services;

use App\NavbarItem;
use App\Settings\AppSettings;
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

    /**
     * @TODO: does not handle conditionally public routes
     */
    public function canAccessRoute($routeName): bool
    {
        $route = Route::getRoutes()->getByName($routeName);
        if (!$route) {
            return false;
        }

        $can = $route->action['can'] ?? [];

        // If the 'can' array is empty, there are no access restrictions.
        if (empty($can)) {
            return true;
        }

        return Gate::any($can);
    }
}
