Analytics.Metadata={getContinentByCode:function(n){var t;return $.each(Analytics.continents,function(e,a){n==a.code&&(t=a.label)}),t?t:n},getSubContinentByCode:function(n){var t;return $.each(Analytics.subContinents,function(e,a){n==a.code&&(t=a.label)}),t?t:n}};