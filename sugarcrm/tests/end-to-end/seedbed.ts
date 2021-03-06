/*
 * Your installation or use of this SugarCRM file is subject to the applicable
 * terms available at
 * http://support.sugarcrm.com/Resources/Master_Subscription_Agreements/.
 * If you do not agree to all of the applicable terms or do not have the
 * authority to bind the entity as an authorized representative, then do not
 * install or use this SugarCRM file.
 *
 * Copyright (C) SugarCRM Inc. All rights reserved.
 */

import * as _ from 'lodash';
import LoginLayout from './layouts/login-layout';
import RecordLayout from './layouts/record-layout';
import ListLayout from './layouts/list-layout';
import PreviewLayout from './layouts/preview-layout';
import {Seedbed} from '@sugarcrm/seedbed';
import DrawerLayout from './layouts/drawer-layout';
import QliRecord from './views/qli-record';
import CommentRecord from './views/comment-record';
import GroupRecord from './views/group-record';
import DrawerLayoutOpp from "./layouts/drawer-layout-opp";
import SearchAndAddLayout from "./layouts/searchAndAdd-layout";
import PersonalInfoDrawerLayout from "./layouts/personal-info-drawer-layout";


export default (seedbed: Seedbed) => {

    seedbed.cucumber.addAsyncHandler('Before', async ({ scenario }) => {
        seedbed.cachedRecords.clear();
    });

    /*runs as soon as log in page is loaded and metadata that is available at that moment saved*/
    seedbed.addAsyncHandler(seedbed.events.BEFORE_INIT, async () => {

        seedbed.defineComponent('Login', LoginLayout, {module: 'Login'});

        let userSettings: any = seedbed.config.users.default;

        await seedbed.api.updatePreferences({
            preferences: userSettings.defaultPreferences,
        });
    });

// is called after cukes init, one time
    seedbed.addAsyncHandler(seedbed.events.AFTER_INIT, () => {

        seedbed.defineComponent(`OpportunityDrawer`, DrawerLayoutOpp, {module: 'Opportunities'});

        /*cache drawers for modules*/
        _.each(seedbed.meta.modules, (module, moduleName) => {

            seedbed.defineComponent(`${moduleName}List`, ListLayout, {module: moduleName});

            // If module supports "RecordLayout" let's pre-create it
            if (module.views && module.views.record) {
                seedbed.defineComponent(`${moduleName}Record`, RecordLayout, {module: moduleName});
                seedbed.defineComponent(`${moduleName}Drawer`, DrawerLayout, {module: moduleName});
                seedbed.defineComponent(`${moduleName}SearchAndAdd`, SearchAndAddLayout, {module: moduleName});
                seedbed.defineComponent(`PersonalInfoDrawer`, PersonalInfoDrawerLayout, {module: moduleName});
            }
        });

    });

    /**
     * After login we need to define layouts
     * based on cached records test created
     */
    seedbed.addAsyncHandler(seedbed.events.LOGIN, () => {
        seedbed.cachedRecords.iterate((record, recordAlias) => {

            if (record.module) {

                // Define Detail Layout for cached record
                seedbed.defineComponent(`${recordAlias}Record`, RecordLayout, {
                    module: record.module,
                    id: record.id
                });

                seedbed.defineComponent(`${recordAlias}Drawer`, DrawerLayout, {
                    module: record.module,
                    id: record.id
                });

                seedbed.components[`${record.module}List`].ListView.createListItem(record);
            }

        }, this);

    });

// is called after waitForApp, each time
    seedbed.addAsyncHandler(seedbed.events.SYNC, clientInfo => {

        let createdRecords = clientInfo.create;

        let recordsInfo = _.filter(seedbed.cucumber.scenario.recordsInfo, (_recordInfo: any) => !_recordInfo.recordId);

        let recordInfo: any = null;

        createdRecords = _.filter(createdRecords, (createdRecord: any) => !seedbed.cachedRecords.findAlias(_item => _item.id === createdRecord.id));

        let item = _.find(createdRecords, (createdRecord: any) => {

            recordInfo = _.find(recordsInfo, (_recordInfo: any) => {
                /*
                 We need to make sure we find correct record to be updated
                 Why need this fix: Sugar do POST requests on Dashboards to create them, if not available (for new installs)
                 Those POST requests are pushed to clientInfo.create and assigned to wrong seedbed.scenario.recordsInfo[] elements
                 */
                return _recordInfo.uid &&
                    createdRecord._module &&
                    createdRecord._module === _recordInfo.module;
            });
            return !!recordInfo;

        });

        if (recordInfo && !seedbed.cachedRecords.contains(recordInfo.uid)) {

            seedbed.cachedRecords.push(
                recordInfo.uid,
                {
                    input: recordInfo.input,
                    id: item.id,
                    module: recordInfo.module
                }
            );

            recordInfo.recordId = item.id;

            if (recordInfo.module === 'ProductBundles') {
                seedbed.defineComponent(`${recordInfo.uid}GroupRecord`, GroupRecord, {
                    id: item.id,
                });
                return;
            }

            if (recordInfo.module === 'ProductBundleNotes') {
                seedbed.defineComponent(`${recordInfo.uid}CommentRecord`, CommentRecord, {
                    id: item.id,
                });
                return;
            }

            if (recordInfo.module === 'Products') {

                seedbed.defineComponent(`${recordInfo.uid}QLIRecord`, QliRecord, {
                    id: item.id,
                });
            }

            seedbed.defineComponent(`${recordInfo.uid}Record`, RecordLayout, {
                module: recordInfo.module,
                id: item.id
            });

            seedbed.defineComponent(`${recordInfo.uid}Drawer`, DrawerLayout, {
                module: recordInfo.module,
                id: item.id
            });

            seedbed.defineComponent(`${recordInfo.uid}Preview`, PreviewLayout, {
                module: recordInfo.module,
                id: recordInfo.id,
            });

        }

    });
    seedbed.addAsyncHandler(seedbed.events.RESPONSE, (data, req, res) => {

        if (req.method === 'POST' && /(\/opportunity)/.test(req.url)) {

            let responseRecord = JSON.parse(data.buffer.toString());
            responseRecord = responseRecord.record;

            /* find record info for created record */
            let recordInfo: any = _.find(seedbed.cucumber.scenario.recordsInfo, (record: any) => {
                return responseRecord && responseRecord.id && responseRecord.id === record.recordId;
            });

            // TODO: it's a temporary solution, we need to create views for this record, see
            // Scenario: Quotes > Create Opportunity
            if (!recordInfo) {
                seedbed.api.created.push(responseRecord);
            }

        }

    });

    seedbed.addAsyncHandler(seedbed.events.RESPONSE, (data, req, res) => {

        let url = req.url,
            responseData;

        /*Cache Activities records when Activities stream is loaded*/
        if ((parseInt(res.statusCode, 10) === 200) &&
            _.includes(['POST', 'PUT'], req.method) &&
            !/(oauth2|bulk|filter)/.test(url)) {

            responseData = JSON.parse(data.buffer.toString());

            let responseRecord = responseData.related_record || responseData;

            if (_.includes(['POST'], req.method) && url.indexOf('/file/filename') === -1) {

                /*find record info for created record*/
                let recordInfo: any = _.find(seedbed.cucumber.scenario.recordsInfo, (record: any) => {
                    return responseRecord && responseRecord.id && responseRecord.id === record.recordId;
                });

                /*save record in cachedRecords by uid*/
                if (recordInfo && recordInfo.uid) {

                    let record = seedbed.cachedRecords.push(recordInfo.uid, {
                        input: recordInfo.input,
                        id: responseRecord.id,
                        module: recordInfo.module
                    });

                    if (recordInfo.module === 'ProductBundleNotes') {
                        seedbed.defineComponent(`${recordInfo.uid}CommentRecord`, CommentRecord, {
                            id: responseRecord.id,
                        });
                        return;
                    }

                    if (record.module === 'Products') {
                        seedbed.defineComponent(`${recordInfo.uid}QLIRecord`, QliRecord, {
                            id: recordInfo.recordId,
                        });
                    }

                    seedbed.defineComponent(`${recordInfo.uid}Record`, RecordLayout, {
                        module: record.module,
                        id: record.id,
                    });

                    seedbed.defineComponent(`${recordInfo.uid}Drawer`, DrawerLayout, {
                        module: record.module,
                        id: record.id,
                    });

                    seedbed.defineComponent(`${recordInfo.uid}Preview`, PreviewLayout, {
                        module: record.module,
                        id: record.id,
                    });

                }
            }
        }
    });
};

