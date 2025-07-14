<?php

namespace M2E\AmazonMcf\Block\Adminhtml;

class General extends \M2E\AmazonMcf\Block\Adminhtml\AbstractBlock
{
    /** @var string */
    protected $_template = 'general.phtml';

    protected function _prepareLayout()
    {
        if ($this->getIsAjax()) {
            return parent::_prepareLayout();
        }

        $this->css->addFile('plugin/AreaWrapper.css');
        $this->css->addFile('plugin/ProgressBar.css');
        $this->css->addFile('help_block.css');
        $this->css->addFile('style.css');
        $this->css->addFile('grid.css');

        $currentView = $this->getCurrentView();
        if (!empty($currentView)) {
            $this->css->addFile($currentView . '/style.css');
        }

        return parent::_prepareLayout();
    }

    private function getCurrentView(): ?string
    {
        $controllerName = $this->getRequest()->getControllerName();

        if ($controllerName === null) {
            return null;
        }

        if (stripos($controllerName, 'amazonmcf') !== false) {
            return 'amazonmcf';
        }

        return null;
    }

    protected function _beforeToHtml()
    {
        if ($this->getIsAjax()) {
            return parent::_beforeToHtml();
        }

        $this->jsUrl->addUrls([
            'm2e_amazonmcf_skin_url' => $this->getViewFileUrl('M2E_AmazonMcf'),
        ]);

        $this->jsTranslator->addTranslations([
            'Are you sure?' => __('Are you sure?'),
            'Confirmation' => __('Confirmation'),
            'Help' => __('Help'),
            'Hide Block' => __('Hide Block'),
            'Show Tips' => __('Show Tips'),
            'Hide Tips' => __('Hide Tips'),
            'Back' => __('Back'),
            'Info' => __('Info'),
            'Warning' => __('Warning'),
            'Error' => __('Error'),
            'Close' => __('Close'),
            'Success' => __('Success'),
            'None' => __('None'),
            'Add' => __('Add'),
            'Save' => __('Save'),
            'Send' => __('Send'),
            'Cancel' => __('Cancel'),
            'Reset' => __('Reset'),
            'Confirm' => __('Confirm'),
            'Submit' => __('Submit'),
            'In Progress' => __('In Progress'),
            'Product(s)' => __('Product(s)'),
            'Continue' => __('Continue'),
            'Complete' => __('Complete'),
            'Yes' => __('Yes'),
            'No' => __('No'),

            'Collapse' => __('Collapse'),
            'Expand' => __('Expand'),

            'Please select the Products you want to perform the Action on.' => __(
                'Please select the Products you want to perform the Action on.'
            ),
            'Please select Items.' => __('Please select Items.'),
            'Please select Action.' => __('Please select Action.'),
            'View Full Product Log' => __('View Full Product Log'),
            'This is a required field.' => __('This is a required field.'),
            'Invalid input data. Decimal value required. Example 12.05' => __(
                'Invalid input data. Decimal value required. Example 12.05'
            ),
            'Email is not valid.' => __('Email is not valid.'),

            'You should select Attribute Set first.' => __('You should select Attribute Set first.'),

            'Create a New One...' => __('Create a New One...'),
            'Creation of New Magento Attribute' => __('Creation of New Magento Attribute'),

            'You should select Store View' => __('You should select Store View'),

            'Insert Magento Attribute in %s%' => __('Insert Magento Attribute in %s%'),
            'Attribute' => __('Attribute'),
            'Insert' => __('Insert'),

            'Settings have been saved.' => __('Settings have been saved.'),
            'You must select at least one Site you will work with.' =>
                __('You must select at least one Site you will work with.'),

            'Preparing to start. Please wait ...' => __('Preparing to start. Please wait ...'),

            'Unauthorized! Please login again' => __('Unauthorized! Please login again'),
        ]);

        return parent::_beforeToHtml();
    }
}
