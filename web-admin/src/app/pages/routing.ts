import { Routes } from '@angular/router';
import { ViewPartnersComponent } from './management/users/partners/view-partners/view-partners.component';
import { ViewClientsComponent } from './management/users/clients/view-clients/view-clients.component';
import { ViewAdminsComponent } from './management/users/admins/view-admins/view-admins.component';
import { ArticlesComponent } from './management/blog/articles/articles.component';
import { NewArticleComponent } from './management/blog/new-article/new-article.component';
import { ViewComplaintsComponent } from './management/complaints/view-complaints/view-complaints.component';
import { EventsComponent } from './partner-side/services/manage-events/events/events.component';
import { ProductsComponent } from './partner-side/services/manage-products/products/products.component';
import { NewEventComponent } from './partner-side/services/manage-events/new-event/new-event.component';
import { ArticlesPartnerComponent } from './partner-side/blog/articles-partner/articles-partner.component';

const Routing: Routes = [
  {
    path: 'dashboard',
    loadChildren: () =>
      import('./dashboard/dashboard.module').then((m) => m.DashboardModule),
  },
  {
    path: 'builder',
    loadChildren: () =>
      import('./builder/builder.module').then((m) => m.BuilderModule),
  },
  {
    path: 'crafted/pages/profile',
    loadChildren: () =>
      import('../modules/profile/profile.module').then((m) => m.ProfileModule),
  },
  {
    path: 'crafted/account',
    loadChildren: () =>
      import('../modules/account/account.module').then((m) => m.AccountModule),
  },
  {
    path: 'crafted/pages/wizards',
    loadChildren: () =>
      import('../modules/wizards/wizards.module').then((m) => m.WizardsModule),
  },
  {
    path: 'crafted/widgets',
    loadChildren: () =>
      import('../modules/widgets-examples/widgets-examples.module').then(
        (m) => m.WidgetsExamplesModule
      ),
  },
  {
    path: 'apps/chat',
    loadChildren: () =>
      import('../modules/apps/chat/chat.module').then((m) => m.ChatModule),
  },
  {
    path: '',
    redirectTo: '/dashboard',
    pathMatch: 'full',
  },
  {
    path: '**',
    redirectTo: 'error/404',
  },
  {
    path: 'users/partners',
    component: ViewPartnersComponent
  },
  {
    path: 'users/clients',
    component: ViewClientsComponent
  },
  {
    path: 'users/admins',
    component: ViewAdminsComponent
  },
  {
    path: 'blog/articles',
    component: ArticlesComponent
  },
  {
    path: 'blog/new-article',
    component: NewArticleComponent
  },
  {
    path: 'complaints',
    component: ViewComplaintsComponent
  },
  {
    path:'partner/services/events',
    component: EventsComponent
  },
  {
    path:'partner/services/new-event',
    component: NewEventComponent
  },
  {
    path: 'partner/services/products',
    component: ProductsComponent
  },
  {
    path: 'partner/blog/articles',
    component: ArticlesPartnerComponent
  }
];

export { Routing };
