/**
 * Account Explorer V2
 */
Analytics.AccountExplorerV2 = Garnish.Base.extend({

    data: null,

    $accountSelect: null,
    $propertySelect: null,
    $viewSelect: null,

    init: function(container, options)
    {
        this.setSettings(options, Analytics.AccountExplorer.defaults);

        this.$container = $(container);

        this.$refreshViewsBtn = $('.refresh-views', this.$container);
        this.$spinner = $('.spinner', this.$container);

        this.$accountSelect = $('.account > select', this.$container);
        this.$propertySelect = $('.property > select', this.$container);
        this.$viewSelect = $('.view > select', this.$container);
        this.$reportingViewIdInput = $('.reporting-view-id', this.$container);

        console.log('reporting view ID: ', this.$reportingViewIdInput.val());


        // Add listeners

        this.addListener(this.$refreshViewsBtn, 'click', 'onRefresh');
        this.addListener(this.$accountSelect, 'change', 'onAccountChange');
        this.addListener(this.$propertySelect, 'change', 'onPropertyChange');
        this.addListener(this.$viewSelect, 'change', 'onViewChange');

        this.requestExplorerData(this.$reportingViewIdInput.val());
    },

    requestExplorerData: function(selectedView)
    {
        this.$spinner.removeClass('hidden');
        this.$refreshViewsBtn.addClass('disabled');

        Craft.postActionRequest('analytics/settings/get-account-explorer-data', {}, $.proxy(function(response, textStatus)
        {
            if (textStatus == 'success')
            {
                if(response.error)
                {
                    alert(response.error);
                }
                else
                {
                    this.parseAccountExplorerData(response);
                }

                if(typeof selectedView != 'undefined' && selectedView) {
                    this.selectView(selectedView);
                }

                this.$spinner.addClass('hidden');
                this.$refreshViewsBtn.removeClass('disabled');
            }
            else
            {
                alert('Couldn’t load account explorer data.');
            }
        }, this));
    },

    parseAccountExplorerData: function(data)
    {
        this.data = data;

        var currentAccountId = this.$accountSelect.val();
        var currentPropertyId = this.$propertySelect.val();
        var currentViewId = this.$viewSelect.val();


        // Add account, property and view options

        this.updateAccountOptions();
        this.updatePropertyOptions();
        this.updateViewOptions();

        if(currentAccountId)
        {
            this.$accountSelect.val(currentAccountId);
            this.$accountSelect.trigger('change');
        }

        if(currentPropertyId)
        {
            this.$propertySelect.val(currentPropertyId);
            this.$propertySelect.trigger('change');
        }

        if(currentViewId)
        {
            this.$viewSelect.val(currentViewId);
            this.$viewSelect.trigger('change');
        }
    },

    selectView: function(viewId)
    {
        if(viewId) {
            console.log('viewId', viewId);
            var account;
            var property;
            var view;

            $.each(this.data.views, function(key, dataView) {
                if(dataView.id == viewId) {
                    view = dataView;
                }
            });

            if(view) {
                $.each(this.data.accounts, function(key, dataAccount) {
                    if(dataAccount.id == view.accountId) {
                        account = dataAccount;
                    }
                });
                $.each(this.data.properties, function(key, dataProperty) {
                    if(dataProperty.id == view.webPropertyId) {
                        property = dataProperty;
                    }
                });
            }

            if(account)
            {
                this.$accountSelect.val(account.id);
                this.$accountSelect.trigger('change');
            }

            if(property)
            {
                this.$propertySelect.val(property.id);
                this.$propertySelect.trigger('change');
            }

            if(view)
            {
                this.$viewSelect.val(view.id);
                this.$viewSelect.trigger('change');
            }

            console.log('data', this.data);
            console.log('account, property, view', account, property, view);
        }
    },

    onRefresh: function()
    {
        this.requestExplorerData();
    },

    onAccountChange: function()
    {
        this.updatePropertyOptions();
        // this.updateViewOptions();
        this.onPropertyChange();
    },

    onPropertyChange: function()
    {
        this.updateViewOptions();
        this.onViewChange();
    },

    onViewChange: function()
    {
        this.$reportingViewIdInput.val(this.$viewSelect.val());
    },

    updateAccountOptions: function()
    {
        $('option', this.$accountSelect).remove();

        if(this.data)
        {
            $.each(this.data.accounts, $.proxy(function(key, account) {
                var $option = $('<option />').appendTo(this.$accountSelect);
                $option.attr('value', account.id);
                $option.text(account.name);
            }, this));
        }
    },

    updatePropertyOptions: function()
    {
        $('option', this.$propertySelect).remove();

        if(this.data)
        {
            $.each(this.data.properties, $.proxy(function(key, property) {
                if(property.accountId == this.$accountSelect.val())
                {
                    var $option = $('<option />').appendTo(this.$propertySelect);
                    $option.attr('value', property.id);
                    $option.text(property.name);
                }
            }, this));
        }
    },

    updateViewOptions: function()
    {
        $('option', this.$viewSelect).remove();

        if(this.data)
        {

            $.each(this.data.views, $.proxy(function(key, view) {
                if(view.webPropertyId == this.$propertySelect.val())
                {
                    var $option = $('<option />').appendTo(this.$viewSelect);
                    $option.attr('value', view.id);
                    $option.text(view.name);
                }
            }, this));
        }
    }
}, {
    defaults: {
    }
});