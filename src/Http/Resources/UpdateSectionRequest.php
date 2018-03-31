<?php

namespace Loaf\Settings\Http\Resources;

use Illuminate\Foundation\Http\FormRequest;
use Loaf\Base\Contracts\Settings\SettingsManager;
use Loaf\Settings\Configuration\Group;
use Loaf\Settings\Configuration\Section;
use Loaf\Settings\SettingsException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UpdateSectionRequest extends FormRequest
{
    /**
     * @var Section
     */
    protected $section;

    /**
     * @var string section path
     */
    protected $section_path;

    /**
     * @var SettingsManager
     */
    protected $settings_manager;

    /**
     * UpdateSectionRequest constructor.
     *
     * @param SettingsManager $settings
     */
    public function __construct( SettingsManager $settings )
    {
        parent::__construct();

        $this->settings_manager = $settings;

        try {
            $this->section = $this->settings_manager->getSection( $this->get('section') );
        } catch (SettingsException $e) {
            throw new NotFoundHttpException( $e->getMessage(), $e->getCode(), $e );
        }

        $this->section_path = $this->section->getPath();
    }

    /**
     * Validate whether the user can update the setting
     *
     * @return bool authorized
     */
    public function authorize()
    {
        return $this->user()->can( 'update', $this->section );
    }

    /**
     * Form validation rules for all fields in the section
     *
     * @return array
     */
    public function rules()
    {
        return $this->getSectionRules( $this->section );
    }

    /**
     * Form validation rules for a specific section
     *
     * @param Section $section
     * @return array
     */
    protected function getSectionRules( Section $section )
    {
        $rules = [];

        foreach( $this->section->groups as $group )
            $rules += $this->getGroupRules( $group );

        return $rules;
    }

    /**
     * Form validation rules for a specific group
     *
     * @param Group $group
     * @return array
     */
    protected function getGroupRules( Group $group )
    {
        $rules = [];

        foreach( $group->fields as $field ) {



        }

        return $rules;
    }
}