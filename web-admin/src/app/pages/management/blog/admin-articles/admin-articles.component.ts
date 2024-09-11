import { ChangeDetectorRef, Component, OnInit } from '@angular/core';
import { MatDialog } from '@angular/material/dialog';
import { Subscription } from 'rxjs';
import { AuthService } from 'src/app/modules/auth';
import { ApiService } from 'src/app/services/api.service';
import { RefreshService } from 'src/app/services/refresh.service';
import { DeleteArticleComponent } from '../delete-article/delete-article.component';
import { UpdateArticleComponent } from '../update-article/update-article.component';
import { NewArticleComponent } from '../new-article/new-article.component';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-admin-articles',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './admin-articles.component.html',
  styleUrl: './admin-articles.component.scss'
})
export class AdminArticlesComponent implements OnInit{
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
  
  openModal(): void {
    const dialogRef = this.dialog.open(NewArticleComponent);

    dialogRef.afterClosed().subscribe(result => {
      console.log('The modal was closed');
    });
  }

  openDeleteModal(id: number): void {
    const dialogRef = this.dialog.open(DeleteArticleComponent, {
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
    const dialogRef = this.dialog.open(UpdateArticleComponent, {
      data: { articleId: articleId }
    });
  
    dialogRef.afterClosed().subscribe(result => {
      if (result) {
        this.refreshService.triggerRefresh('/blog/articles');
      }
    });
  }
}
