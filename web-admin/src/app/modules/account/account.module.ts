import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { AccountRoutingModule } from './account-routing.module';
import { AccountComponent } from './account.component';
import { OverviewComponent } from './overview/overview.component';
import { ConnectedAccountsComponent } from './settings/forms/connected-accounts/connected-accounts.component';
import { DeactivateAccountComponent } from './settings/forms/deactivate-account/deactivate-account.component';
import { EmailPreferencesComponent } from './settings/forms/email-preferences/email-preferences.component';
import { NotificationsComponent } from './settings/forms/notifications/notifications.component';
import { DropdownMenusModule, WidgetsModule } from '../../_metronic/partials';
import {SharedModule} from "../../_metronic/shared/shared.module";

@NgModule({
  declarations: [
    AccountComponent,
    OverviewComponent,
    ConnectedAccountsComponent,
    DeactivateAccountComponent,
    EmailPreferencesComponent,
    NotificationsComponent,
  ],
  imports: [
    CommonModule,
    AccountRoutingModule,
    DropdownMenusModule,
    WidgetsModule,
    SharedModule,
  ],
  exports: [
    
  ]
})
export class AccountModule {}
