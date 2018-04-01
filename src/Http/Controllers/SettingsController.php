<?php

namespace Loaf\Settings\Http\Controllers;

use Illuminate\Http\Request;
use Loaf\Admin\Http\Controllers\Controller;
use Loaf\Base\Contracts\Menu\AdminMenu;
use Loaf\Base\Contracts\Settings\SettingsManager as SettingsManagerContract;
use Loaf\Settings\Configuration\Group;
use Loaf\Settings\Configuration\Section;
use Loaf\Settings\Http\Resources\UpdateSectionRequest;
use Loaf\Settings\SettingsException;
use Loaf\Settings\SettingsManager;
use Loaf\Settings\SettingsParseException;
use Log;

class SettingsController extends Controller
{
    /**
     * @var SettingsManager
     */
    protected $settings_manager;

    /**
     * @var Section
     */
    protected $section;

    public function __construct(AdminMenu $menu_manager, SettingsManagerContract $settings)
    {
        parent::__construct($menu_manager);

        $this->settings_manager = $settings;
    }

    /**
     * Show the section edit form.
     *
     * @param string $section
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function editSection(string $section)
    {
        $section = $this->findOrFailSection($section);

        $this->authorize('view', $section);

        return view('loaf/settings::settings', compact('section'));
    }

    /**
     * Update the section.
     *
     * @param UpdateSectionRequest $request ensures validation of the section
     * @param string               $section
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateSection(UpdateSectionRequest $request, string $section)
    {
        $this->findOrFailSection($section);

        $this->updateSectionSettings($request, $this->section);

        $this->flashStatus('Settings saved');

        return $this->redirectToSectionEdit();
    }

    protected function updateSectionSettings(Request $request, Section $section)
    {
        foreach ($section->groups as $group) {
            $this->updateGroupSettings($request, $group);
        }
    }

    protected function updateGroupSettings(Request $request, Group $group)
    {
        foreach ($group->fields as $field) {
            $type = $this->settings_manager->getSettingType($field);
            $field_path = $field->getPath();

            $form_data = $request->input($type->getFormKey()) ?? [];

            try {
                list($save, $parsed) = $type->parseEditFormData($form_data);
            } catch (SettingsParseException $e) {
                Log::warning("Error parsing $field_path, got: ".$e->getMessage());
                continue;
            }

            if (!$save) {
                continue;
            }

            $this->settings_manager->set(
                $type->getField()->getPath(),
                $parsed
            );
        }
    }

    /**
     * Retrieve a section or abort the request.
     *
     * @param string $section
     * @param int    $abort_code
     *
     * @return \Loaf\Base\Contracts\Settings\ConfigElement
     */
    protected function findOrFailSection(string $section, int $abort_code = 404)
    {
        try {
            return $this->section = $this->settings_manager->getSection($section);
        } catch (SettingsException $e) {
            abort($abort_code);
        }
    }

    /**
     * Redirect to current section edit route.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function redirectToSectionEdit()
    {
        return $this->redirectToSectionRoute('editSection');
    }

    /**
     * Redirect to admin.settings.$action with section key.
     *
     * @param string $action
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function redirectToSectionRoute(string $action)
    {
        return redirect()->route("admin.settings.$action", [
            'section' => $this->section->getPath(),
        ]);
    }
}
