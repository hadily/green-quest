import { NgModule, APP_INITIALIZER } from '@angular/core';
import { BrowserModule } from '@angular/platform-browser';
import { BrowserAnimationsModule } from '@angular/platform-browser/animations';
import { HttpClientModule } from '@angular/common/http';
import { HttpClientInMemoryWebApiModule } from 'angular-in-memory-web-api';
import { ClipboardModule } from 'ngx-clipboard';
import { TranslateModule } from '@ngx-translate/core';
import { InlineSVGModule } from 'ng-inline-svg-2';
import { NgbModule } from '@ng-bootstrap/ng-bootstrap';
import { AppRoutingModule } from './app-routing.module';
import { AppComponent } from './app.component';
import { AuthService } from './modules/auth/services/auth.service';
import { environment } from 'src/environments/environment';
import { provideAnimationsAsync } from '@angular/platform-browser/animations/async';
import { MatDialogModule } from '@angular/material/dialog';
import { FormsModule } from '@angular/forms';
import { NewPartnerComponent } from './pages/management/users/partners/new-partner/new-partner.component';
import { UpdatePartnersComponent } from './pages/management/users/partners/update-partners/update-partners.component';
import { ViewPartnersComponent } from './pages/management/users/partners/view-partners/view-partners.component';
import { ViewClientsComponent } from './pages/management/users/clients/view-clients/view-clients.component';
import { NewClientComponent } from './pages/management/users/clients/new-client/new-client.component';
import { UpdateClientComponent } from './pages/management/users/clients/update-client/update-client.component';
import { DeleteClientComponent } from './pages/management/users/clients/delete-client/delete-client.component';
import { ViewAdminsComponent } from './pages/management/users/admins/view-admins/view-admins.component';
import { NewAdminComponent } from './pages/management/users/admins/new-admin/new-admin.component';
import { UpdateAdminComponent } from './pages/management/users/admins/update-admin/update-admin.component';
import { DeleteAdminComponent } from './pages/management/users/admins/delete-admin/delete-admin.component';

function appInitializer(authService: AuthService) {
  return () => {
    return new Promise((resolve) => {
      // @ts-ignore
      authService.getUserByToken().subscribe().add(resolve);
    });
  };
}

@NgModule({
  declarations: [
    AppComponent,
    NewPartnerComponent,
    UpdatePartnersComponent,
    ViewPartnersComponent,
    ViewClientsComponent,
    NewClientComponent,
    UpdateClientComponent,
    DeleteClientComponent,
    ViewAdminsComponent,
    NewAdminComponent,
    UpdateAdminComponent,
    DeleteAdminComponent
  ],
  imports: [
    BrowserModule,
    BrowserAnimationsModule,
    TranslateModule.forRoot(),
    HttpClientModule,
    ClipboardModule,
    AppRoutingModule,
    InlineSVGModule.forRoot(),
    NgbModule,
    ////////////////////////////
    MatDialogModule,
    FormsModule,
  ],
  providers: [
    {
      provide: APP_INITIALIZER,
      useFactory: appInitializer,
      multi: true,
      deps: [AuthService],
    },
    provideAnimationsAsync(),
  ],
  bootstrap: [AppComponent],
})
export class AppModule {}
