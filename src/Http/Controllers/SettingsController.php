<?php

namespace Loaf\Settings\Http\Controllers;

use Loaf\Admin\Http\Controllers\Controller;
use Loaf\Base\Contracts\Menu\AdminMenu;
use Loaf\Base\Contracts\Settings\SettingsManager;
use Loaf\Settings\Configuration\Section;
use Loaf\Settings\Http\Resources\UpdateSectionRequest;
use Loaf\Settings\SettingsException;

use Session;

class SettingsController extends Controller
{
    /**
     * @var SettingsManager
     */
    protected $settings;

    /**
     * @var Section
     */
    protected $section;

    public function __construct( AdminMenu $menu_manager, SettingsManager $settings )
    {
        parent::__construct($menu_manager);

        $this->settings = $settings;
    }

    /**
     * Show the section edit form
     *
     * @param string $section
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function editSection( string $section )
    {
        $section = $this->findOrFailSection( $section );

        $this->authorize('view', $section);

        return view('loaf/settings::settings', compact('section'));
    }

    /**
     * Update the section
     *
     * @param UpdateSectionRequest $request ensures validation of the section
     * @param string $section
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateSection( UpdateSectionRequest $request, string $section )
    {
        $this->findOrFailSection( $section );

        $this->flashStatus('Updating group... not implemented yet', false, 'warning');

        return $this->redirectToSectionEdit();
    }

    /**
     * Retrieve a section or abort the request
     *
     * @param string $section
     * @param int $abort_code
     * @return \Loaf\Base\Contracts\Settings\ConfigElement
     */
    protected function findOrFailSection( string $section, int $abort_code = 404 )
    {
        try {
            return $this->section = $this->settings->getSection( $section );
        } catch (SettingsException $e) {
            abort( $abort_code );
        }
    }

    /**
     * Redirect to current section edit route
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function redirectToSectionEdit()
    {
        return $this->redirectToSectionRoute("editSection");
    }

    /**
     * Redirect to admin.settings.$action with section key
     *
     * @param string $action
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function redirectToSectionRoute(string $action)
    {
        return redirect()->route("admin.settings.$action", [
            'section' => $this->section->getPath()
        ]);
    }
}
