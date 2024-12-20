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
import { EventsComponent } from './pages/partner-side/services/manage-events/events/events.component';
import { EventPanelComponent } from './pages/partner-side/services/manage-events/event-panel/event-panel.component';
import { NewEventComponent } from './pages/partner-side/services/manage-events/new-event/new-event.component';
import { ProductsComponent } from './pages/partner-side/services/manage-products/products/products.component';
import { NewProductComponent } from './pages/partner-side/services/manage-products/new-product/new-product.component';
import { DeleteProductComponent } from './pages/partner-side/services/manage-products/delete-product/delete-product.component';
import { UpdateProductComponent } from './pages/partner-side/services/manage-products/update-product/update-product.component';
import { DeleteEventComponent } from './pages/partner-side/services/manage-events/delete-event/delete-event.component';
import { NewArticlePartnerComponent } from './pages/partner-side/blog/new-article-partner/new-article-partner.component';
import { ArticlesPartnerComponent } from './pages/partner-side/blog/articles-partner/articles-partner.component';
import { DeleteArticlePartnerComponent } from './pages/partner-side/blog/delete-article-partner/delete-article-partner.component';
import { UpdateArticlePartnerComponent } from './pages/partner-side/blog/update-article-partner/update-article-partner.component';
import { PartnerComplaintsComponent } from './pages/partner-side/complaints/partner-complaints/partner-complaints.component';
import { PartnerCreateComplaintComponent } from './pages/partner-side/complaints/partner-create-complaint/partner-create-complaint.component';
import { UpdateAdminArticleComponent } from './pages/management/blog/update-admin-article/update-admin-article.component';
import { BookingsComponent } from './pages/partner-side/services/manage-events/bookings/bookings.component';
import { BookingsProdComponent } from './pages/partner-side/services/manage-products/bookings-prod/bookings-prod.component';

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
    EventsComponent,
    EventPanelComponent,
    NewEventComponent,
    DeleteEventComponent,
    ProductsComponent,
    NewProductComponent,
    UpdateProductComponent,
    DeleteProductComponent,
    ArticlesPartnerComponent,
    NewArticlePartnerComponent,
    DeleteArticlePartnerComponent,
    UpdateArticlePartnerComponent,
    PartnerComplaintsComponent,
    PartnerCreateComplaintComponent,
    UpdateAdminArticleComponent,
    BookingsComponent,
    BookingsProdComponent
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
    {provide: HTTP_INTERCEPTORS, useClass: AuthInterceptor, multi: true},
  ],
  bootstrap: [AppComponent],
})
export class AppModule {}
