<?php

namespace Modules\Magnati\Admin;

use Modules\Admin\Ui\Tab;
use Modules\Admin\Ui\Tabs;
use Modules\Support\Locale;
use Modules\Support\Country;

class MagnatiTabs extends Tabs
{
    /**
     * Make new tabs with form.
     *
     * @return void
     */
    public function make()
    {
        $this->group('magnati_settings', trans('magnati::magnati.tabs.group.magnati_settings'))
            ->active()
            ->add($this->general())
            ->add($this->credentials());
    }

    /**
     * Get the general tab.
     *
     * @return \Modules\Admin\Ui\Tab
     */
    private function general()
    {
        return tap(new Tab('general', trans('magnati::magnati.tabs.general')), function (Tab $tab) {
            $tab->active()
                ->weight(5)
                ->fields([
                    'magnati_enabled',
                    'translatable.magnati_label',
                    'translatable.magnati_description',
                    'magnati_test_mode',
                ]);
        });
    }

    /**
     * Get the credentials tab.
     *
     * @return \Modules\Admin\Ui\Tab
     */
    private function credentials()
    {
        return tap(new Tab('credentials', trans('magnati::magnati.tabs.credentials')), function (Tab $tab) {
            $tab->weight(10)
                ->fields([
                    'magnati_test_username',
                    'magnati_test_password',
                    'magnati_test_customer',
                    'magnati_test_store',
                    'magnati_test_terminal',
                    'magnati_test_transaction_hint',
                    'magnati_username',
                    'magnati_password',
                    'magnati_customer',
                    'magnati_store',
                    'magnati_terminal',
                    'magnati_transaction_hint',
                ]);
        });
    }
}
