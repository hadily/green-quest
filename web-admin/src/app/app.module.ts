import { NgModule, APP_INITIALIZER } from '@angular/core';
import { BrowserModule } from '@angular/platform-browser';
import { BrowserAnimationsModule } from '@angular/platform-browser/animations';
import { HTTP_INTERCEPTORS, HttpClientModule } from '@angular/common/http';
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
import { AuthInterceptor } from './interceptors/auth.interceptor';
import { ArticlesComponent } from './pages/management/blog/articles/articles.component';
import { DeleteArticleComponent } from './pages/management/blog/delete-article/delete-article.component';
import { UpdateArticleComponent } from './pages/management/blog/update-article/update-article.component';
import { NewArticleComponent } from './pages/management/blog/new-article/new-article.component';
import { ViewComplaintsComponent } from './pages/management/complaints/view-complaints/view-complaints.component';
import { UpdateComplaintsComponent } from './pages/management/complaints/update-complaints/update-complaints.component';
import { ProfileDetailsComponent } from './modules/account/settings/forms/profile-details/profile-details.component';
import { SettingsComponent } from './modules/account/settings/settings.component';
import { SignInMethodComponent } from './modules/account/settings/forms/sign-in-method/sign-in-method.component';
import { SharedModule } from './_metronic/shared/shared.module';
import { AccountModule } from './modules/account/account.module';

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
    DeleteAdminComponent,
    ArticlesComponent,
    DeleteArticleComponent,
    UpdateArticleComponent,
    NewArticleComponent,
    ViewComplaintsComponent,
    UpdateComplaintsComponent,
    SettingsComponent,
    SignInMethodComponent,
    ProfileDetailsComponent
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
    SharedModule,
    AccountModule,
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
    {provide: HTTP_INTERCEPTORS, useClass: AuthInterceptor, multi: true},
  ],
  bootstrap: [AppComponent],
})
export class AppModule {}
