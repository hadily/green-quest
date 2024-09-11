import { ChangeDetectorRef, Component, OnInit } from '@angular/core';
import { MatDialog } from '@angular/material/dialog';
import { Subscription } from 'rxjs';
import { AuthService } from 'src/app/modules/auth';
import { ApiService } from 'src/app/services/api.service';
import { RefreshService } from 'src/app/services/refresh.service';
import { DeleteArticlePartnerComponent } from '../delete-article-partner/delete-article-partner.component';
import { NewArticlePartnerComponent } from '../new-article-partner/new-article-partner.component';
import { UpdateArticlePartnerComponent } from '../update-article-partner/update-article-partner.component';

@Component({
  selector: 'app-articles-partner',
  templateUrl: './articles-partner.component.html',
  styleUrl: './articles-partner.component.scss'
})
export class ArticlesPartnerComponent implements OnInit{
  articles: any[] = []; 
  allArticles: any[] = [];
  private refreshSubscription: Subscription;

  constructor(
    public dialog: MatDialog, 
    private apiService: ApiService,
    private cdr: ChangeDetectorRef,
    private refreshService: RefreshService,
    private authService: AuthService
  ) {}

  ngOnInit(): void {
    this.loadArticles();
    this.refreshSubscription = this.refreshService.getRefreshObservable().subscribe(() => {
      this.loadArticles(); 
    });
  }

  loadArticles() {
    const userId = this.authService.currentUserValue?.id ?? 1;
    console.log("userId ", userId);
    this.apiService.getAllArticlesByWriter(userId).subscribe(
        data => {
            console.log("Fetched articles:", data);
            this.articles = data;
            this.allArticles = this.articles;
            this.cdr.detectChanges();
        },
        error => {
            console.error("Error fetching data:", error);
        }
    );
  }

  getBadgeClass(status: string): string {
    switch (status) {
      case 'Approved':
        return 'badge-light-success';
      case 'Pending':
        return 'badge-light-warning';
      case 'Reviewed':
        return 'badge-light-danger';
      case 'Updated':
        return 'badge-light-primary'; 
      default:
        return 'badge-light-warning';
    }
  }
  
  openModal(): void {
    const dialogRef = this.dialog.open(NewArticlePartnerComponent);

    dialogRef.afterClosed().subscribe(result => {
      console.log('The modal was closed');
    });
  }

  openDeleteModal(id: number): void {
    const dialogRef = this.dialog.open(DeleteArticlePartnerComponent, {
      data: { id } 
    });

    dialogRef.afterClosed().subscribe(result => {
      if (result) {
        console.log('Deletion confirmed.');
      } else {
        console.log('Deletion canceled.');
      }
    });
  }

  openUpdateModal(articleId: number): void {
    const dialogRef = this.dialog.open(UpdateArticlePartnerComponent, {
      data: { articleId: articleId }
    });
  
    dialogRef.afterClosed().subscribe(result => {
      if (result) {
        this.refreshService.triggerRefresh('/partner/blog/articles');
      }
    });
  }
}
