# Your installation or use of this SugarCRM file is subject to the applicable
# terms available at
# http://support.sugarcrm.com/Resources/Master_Subscription_Agreements/.
# If you do not agree to all of the applicable terms or do not have the
# authority to bind the entity as an authorized representative, then do not
# install or use this SugarCRM file.
#
# Copyright (C) SugarCRM Inc. All rights reserved.

@quotes
Feature: Generate Quote From RLI subpanel in Opportunity record view

  Background:
    Given I use default account
    Given I launch App


  @generate_quote_from_multiple_RLIs
  Scenario: Opportunity Record View > RLI Subpanel > Generate Quote From Multiple RLI Records
    # 1. Create required records
    Given Opportunities records exist:
      | *name |
      | Opp_1 |
    Given Accounts records exist related via accounts link to *Opp_1:
      | *name |
      | Acc_1 |
    Given ProductTemplates records exist:
      | *name  | discount_price | cost_price | list_price |
      | Prod_1 | 100            | 200        | 300        |
      | Prod_2 | 100            | 200        | 300        |

    Given I open about view and login

    # 2. Add new 'Euro' currency to Sugar instance
    When I go to "Currencies" url
    When I click Create button on #CurrenciesList header
    When I provide input for #CurrenciesDrawer.RecordView view
      | iso4217 | conversion_rate |
      | EUR     | 0.5             |
    When I click Save button on #CurrenciesDrawer header
    When I close alert

    # 3. Add new 'Rubles' currency to Sugar instance
    When I click Create button on #CurrenciesList header
    When I provide input for #CurrenciesDrawer.RecordView view
      | iso4217 | conversion_rate |
      | RUB     | 1.5             |
    When I click Save button on #CurrenciesDrawer header
    When I close alert

    # 4. Change 'Prod_1' product's currency to EUR
    When I go to "ProductTemplates" url
    When I select *Prod_1 in #ProductTemplatesList.ListView
    Then I should see #Prod_1Record view
    When I click Edit button on #Prod_1Record header
    When I provide input for #Prod_1Record.RecordView view
      | currency_id |
      | € (EUR)     |
    When I click Save button on #Prod_1Record header
    When I close alert

    # 5. Change 'Prod_2' product's currency to RUB
    When I go to "ProductTemplates" url
    When I select *Prod_2 in #ProductTemplatesList.ListView
    Then I should see #Prod_2Record view
    When I click Edit button on #Prod_2Record header
    When I provide input for #Prod_2Record.RecordView view
      | currency_id |
      | руб (RUB)   |
    When I click Save button on #Prod_2Record header
    When I close alert

    When I choose Opportunities in modules menu
    When I select *Opp_1 in #OpportunitiesList.ListView
    When I close alert

    # 6. Create 'RLI_1' record from RLI subpanel of opportunity record view using EUR currency
    When I open the revenuelineitems subpanel on #Opp_1Record view
    When I create_new record from revenuelineitems subpanel on #Opp_1Record view
    When I provide input for #RevenueLineItemsDrawer.HeaderView view
      | *     | name  |
      | RLI_1 | RLI_1 |
    When I provide input for #RevenueLineItemsDrawer.RecordView view
      | *     | date_closed | discount_price | quantity | currency_id | discount_amount |
      | RLI_1 | 12/12/2020  | 175.00         | 3.75     | € (EUR)     | 75.34           |
    When I click Save button on #RevenueLineItemsDrawer header
    When I close alert

    # 7. Verify 'RLI_1' data in RLI subpanel of Opportunity record view
    When I open the revenuelineitems subpanel on #Opp_1Record view
    Then I verify fields for *RLI_1 in #Opp_1Record.SubpanelsLayout.subpanels.revenuelineitems
      | fieldName   | value         |
      | name        | RLI_1         |
      | date_closed | 12/12/2020    |
      | likely_case | €87.50$175.00 |

    # 8. Create 'RLI_2' record from RLI subpanel of opportunity record view using RUB currency
    When I open the revenuelineitems subpanel on #Opp_1Record view
    When I create_new record from revenuelineitems subpanel on #Opp_1Record view
    When I provide input for #RevenueLineItemsDrawer.HeaderView view
      | *     | name  |
      | RLI_2 | RLI_2 |
    When I provide input for #RevenueLineItemsDrawer.RecordView view
      | *     | date_closed | discount_price | quantity | currency_id | discount_amount |
      | RLI_2 | 12/12/2021  | 84.99          | 50.7     | руб (RUB)   | 84.99           |
    When I click Save button on #RevenueLineItemsDrawer header
    When I close alert

    # 9. Verify 'RLI_2' data in RLI subpanel of Opportunity record view
    When I open the revenuelineitems subpanel on #Opp_1Record view
    Then I verify fields for *RLI_2 in #Opp_1Record.SubpanelsLayout.subpanels.revenuelineitems
      | fieldName   | value           |
      | name        | RLI_2           |
      | date_closed | 12/12/2021      |
      | likely_case | руб127.49$84.99 |

    # 10. Create 'RLI_3' record from RLI subpanel of opportunity record view using RUB currency
    When I open the revenuelineitems subpanel on #Opp_1Record view
    When I create_new record from revenuelineitems subpanel on #Opp_1Record view
    When I provide input for #RevenueLineItemsDrawer.HeaderView view
      | *     | name  |
      | RLI_3 | RLI_3 |
    When I provide input for #RevenueLineItemsDrawer.RecordView view
      | *     | date_closed | product_template_name |
      | RLI_3 | 12/12/2021  | Prod_1                |
    When I click Save button on #RevenueLineItemsDrawer header
    When I close alert

    # 11. Verify 'RLI_3' data in RLI subpanel of Opportunity record view
    When I open the revenuelineitems subpanel on #Opp_1Record view
    Then I verify fields for *RLI_3 in #Opp_1Record.SubpanelsLayout.subpanels.revenuelineitems
      | fieldName   | value         |
      | name        | RLI_3         |
      | date_closed | 12/12/2021    |
      | likely_case | €50.00$100.00 |

    # 12. Create 'RLI_4' record from RLI subpanel of opportunity record view using RUB currency
    When I open the revenuelineitems subpanel on #Opp_1Record view
    When I create_new record from revenuelineitems subpanel on #Opp_1Record view
    When I provide input for #RevenueLineItemsDrawer.HeaderView view
      | *     | name  |
      | RLI_4 | RLI_4 |
    When I provide input for #RevenueLineItemsDrawer.RecordView view
      | *     | date_closed | product_template_name |
      | RLI_4 | 12/14/2021  | Prod_2                |
    When I click Save button on #RevenueLineItemsDrawer header
    When I close alert

    # 13. Verify 'RLI_4' data in RLI subpanel of Opportunity record view
    When I open the revenuelineitems subpanel on #Opp_1Record view
    Then I verify fields for *RLI_4 in #Opp_1Record.SubpanelsLayout.subpanels.revenuelineitems
      | fieldName   | value            |
      | name        | RLI_4            |
      | date_closed | 12/14/2021       |
      | likely_case | руб150.00$100.00 |

    # 14. Verify rollup data in Opportunity
    Then I verify fields on #Opp_1Record.RecordView
      | fieldName   | value      |
      | amount      | $459.99    |
      | best_case   | $459.99    |
      | worst_case  | $459.99    |
      | date_closed | 12/14/2021 |

    # 15. Generate quote
    When I toggleAll records in #Opp_1Record.SubpanelsLayout.subpanels.revenuelineitems
    When I select GenerateQuote action in #Opp_1Record.SubpanelsLayout.subpanels.revenuelineitems
      # Complete quote record and save
    When I toggle Billing_and_Shipping panel on #QuotesRecord.RecordView view
    When I provide input for #QuotesRecord.HeaderView view
      | *      | name          |
      | Quote1 | SugarCRM Inc. |
    When I provide input for #QuotesRecord.RecordView view
      | *      | date_quote_expected_closed |
      | Quote1 | 12/12/2020                 |
    Then I verify fields on #QuotesRecord.RecordView
      | fieldName            | value |
      | billing_account_name | Acc_1 |
    When I click Save button on #QuotesRecord header
    When I close alert

    # 16. Verify that Quote status is now Converted
    Then I verify fields on #QuotesRecord.HeaderView
      | fieldName | value     |
      | converted | Converted |
    Then I verify fields on #QuotesRecord.RecordView
      | fieldName        | value |
      | opportunity_name | Opp_1 |

    # 17. Verify that numbers in Grand Total header are correct
    Then I verify fields on QLI total header on #QuotesRecord view
      | fieldName | value     |
      | deal_tot  | 4.01%     |
      | new_sub   | $4,957.90 |
      | tax       | $0.00     |
      | shipping  | $0.00     |
      | total     | $4,957.90 |

    # 18. Verify data in Quotes subpanel of Opportunity record view
    When I choose Opportunities in modules menu
    When I select *Opp_1 in #OpportunitiesList.ListView
    When I open the quotes subpanel on #Opp_1Record view
    Then I verify fields for *Quote1 in #Opp_1Record.SubpanelsLayout.subpanels.quotes
      | fieldName                  | value         |
      | name                       | SugarCRM Inc. |
      | date_quote_expected_closed | 12/12/2020    |
      | total_usdollar             | $4,957.90     |

    # 19. Verify that all 4 RLIs have link to generated quote in the list view
    When I choose RevenueLineItems in modules menu
    Then I verify fields for *RLI_1 in #RevenueLineItemsList.ListView
      | fieldName  | value         |
      | quote_name | SugarCRM Inc. |
    Then I verify fields for *RLI_2 in #RevenueLineItemsList.ListView
      | fieldName  | value         |
      | quote_name | SugarCRM Inc. |
    Then I verify fields for *RLI_3 in #RevenueLineItemsList.ListView
      | fieldName  | value         |
      | quote_name | SugarCRM Inc. |
    Then I verify fields for *RLI_4 in #RevenueLineItemsList.ListView
      | fieldName  | value         |
      | quote_name | SugarCRM Inc. |

    # 20. Verify that 'RLI_1' record have a label 'Quoted' in the header and link to the generated quote in record view
    Then I should see *RLI_1 in #RevenueLineItemsList.ListView
    When I select *RLI_1 in #RevenueLineItemsList.ListView
    Then I should see #RLI_1Record view
    Then I verify fields on #RLI_1Record.HeaderView
      | fieldName | value  |
      | quote_id  | Quoted |
    Then I verify fields on #RLI_1Record.RecordView
      | fieldName  | value         |
      | quote_name | SugarCRM Inc. |


  @delete_RLIs_from_RLISubpane_of_OpportunityRecordView
  Scenario: Opportunity Record View > RLI Subpanel > Delete All RLIs
    Given Accounts records exist:
      | name  |
      | Acc_1 |
    Given I open about view and login
    When I choose Opportunities in modules menu
    When I click Create button on #OpportunitiesList header
    When I provide input for #OpportunitiesDrawer.HeaderView view
      | *     | name                  |
      | Opp_1 | CreateOpportunityTest |
    When I provide input for #OpportunitiesDrawer.RecordView view
      | *     | account_name |
      | Opp_1 | Acc_1        |
      # Provide input for the first (default) RLI
    When I provide input for #OpportunityDrawer.RLITable view for 1 row
      | name | date_closed | best_case | sales_stage   | quantity | likely_case |
      | RLI1 | 12/12/2020  | 300       | Qualification | 5        | 200         |
      # Add second RLI by clicking '+' button on the first row
    When I choose addRLI on #OpportunityDrawer.RLITable view for 1 row
      # Provide input for the second RLI
    When I provide input for #OpportunityDrawer.RLITable view for 2 row
      | name | date_closed | best_case | sales_stage   | quantity | likely_case |
      | RLI2 | 12/12/2021  | 500       | Qualification | 10       | 400         |
      # Add third RLI by clicking '+' button on the second row
    When I choose addRLI on #OpportunityDrawer.RLITable view for 2 row
      # Provide input for the third RLI
    When I provide input for #OpportunityDrawer.RLITable view for 3 row
      | name | date_closed | best_case | sales_stage   | quantity | likely_case |
      | RLI3 | 12/12/2022  | 50        | Qualification | 10       | 40          |
      # Remove first RLI
    When I choose removeRLI on #OpportunityDrawer.RLITable view for 1 row
      # Save new opportunity
    When I click Save button on #OpportunitiesDrawer header
    When I close alert

    When I select *Opp_1 in #OpportunitiesList.ListView
    When I open the revenuelineitems subpanel on #Opp_1Record view
    When I toggleAll records in #Opp_1Record.SubpanelsLayout.subpanels.revenuelineitems
      #When I select GenerateQuote action in #Opp_1Record.SubpanelsLayout.subpanels.revenuelineitems
    When I select Delete action in #Opp_1Record.SubpanelsLayout.subpanels.revenuelineitems
    When I Confirm confirmation alert
    When I close alert
    Then I verify fields on #Opp_1Record.RecordView
      | fieldName  | value |
      | amount     | $0.00 |
      | best_case  | $0.00 |
      | worst_case | $0.00 |
