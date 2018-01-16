<?php
/**
 * Froala WYSIWYG Editor for Craft
 *
 * @package froalaeditor
 * @author Bert Oost
 */

namespace Craft;

class FroalaEditorPlugin extends BasePlugin
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return Craft::t('Froala WYSIWYG Editor');
    }

    /**
     * {@inheritdoc}
     */
    public function getVersion()
    {
        return '2.2.7.0';
    }

    /**
     * {@inheritdoc}
     */
    public function getReleaseFeedUrl()
    {
        return 'https://raw.githubusercontent.com/froala/Craft-Froala-WYSIWYG/v2/releases.json';
    }

    /**
     * {@inheritdoc}
     */
    public function getDeveloper()
    {
        return 'Bert Oost';
    }

    /**
     * {@inheritdoc}
     */
    public function getDeveloperUrl()
    {
        return 'http://bertoost.com';
    }

    /**
     * {@inheritdoc}
     */
    public function getDocumentationUrl()
    {
        return 'https://github.com/froala/Craft-Froala-WYSIWYG/blob/v2/README.md';
    }

    /**
     * {@inheritdoc}
     */
    public function onAfterInstall()
    {
        // Convert all existing Rich Text fields to Froala Editor
        craft()->db->createCommand()->update(
            'fields',
            array('type' => 'FroalaEditor'),
            array('type' => 'RichText')
        );
    }

    /**
     * {@inheritdoc}
     */
    public function onBeforeUninstall()
    {
        // Convert all existing Froala Editor fields back to Rich Text
        craft()->db->createCommand()->update(
            'fields',
            array('type' => 'RichText'),
            array('type' => 'FroalaEditor')
        );
    }

    /**
     * {@inheritdoc
     */
    public function getSettingsHtml()
    {
        return craft()->templates->render('froalaeditor/settings', array(
            'settings' => $this->getSettings(),
            'editorPlugins' => $this->getEditorPlugins(),
        ));
    }

    /**
     * {@inheritdoc}
     */
    protected function defineSettings()
    {
        return array(
            'licenseKey' => array(AttributeType::String),
            'customCssType' => array(AttributeType::String),
            'customCssFile' => array(AttributeType::String),
            'customCssClasses' => array(AttributeType::String),
            'enabledPlugins' => array(AttributeType::Mixed),
        );
    }

    /**
     * Returns all possible plugins for the editor
     * @return array
     */
    public function getEditorPlugins()
    {
        $pluginDir = __DIR__ . DIRECTORY_SEPARATOR;
        $pluginDir .= implode(DIRECTORY_SEPARATOR, array(
            'resources', 'lib', 'v' . $this->getVersion(), 'js', 'plugins'
        )) . DIRECTORY_SEPARATOR;

        $plugins = array();
        foreach (glob($pluginDir . '*.min.js') as $pluginFile) {
            $fileName = basename($pluginFile);
            $pluginName = str_replace('.min.js', '', $fileName);

            $pluginLabel = str_replace('_', ' ', $pluginName);
            $pluginLabel = ucwords($pluginLabel);

            $plugins[$pluginName] = $pluginLabel;
        }

        return $plugins;
    }

    /**
     * @param string $msg
     * @param string $logLevel
     * @param bool $force
     * @return void
     */
    public static function log($msg, $logLevel = LogLevel::Info, $force = false)
    {
        Craft::log($msg, $logLevel, $force, 'application', 'FroalaEditor');
    }
}