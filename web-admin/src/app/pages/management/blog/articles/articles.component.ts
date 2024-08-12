import { ChangeDetectorRef, Component, OnInit } from '@angular/core';
import { MatDialog } from '@angular/material/dialog';
import { Subscription } from 'rxjs';
import { ApiService } from 'src/app/services/api.service';
import { RefreshService } from 'src/app/services/refresh.service';
import { format, startOfWeek, endOfWeek } from 'date-fns';
import { DeleteArticleComponent } from '../delete-article/delete-article.component';
import { UpdateArticleComponent } from '../update-article/update-article.component';



@Component({
  selector: 'app-articles',
  templateUrl: './articles.component.html',
  styleUrl: './articles.component.scss'
})
export class ArticlesComponent implements OnInit {
  articles: any[] = []; 
  allArticles: any[] = [];
  private refreshSubscription: Subscription;
  searchQuery: string = '';


  constructor(
    public dialog: MatDialog, 
    private apiService: ApiService,
    private cdr: ChangeDetectorRef,
    private refreshService: RefreshService
  ) {}

  ngOnInit(): void {
    this.loadArticles();
    this.refreshSubscription = this.refreshService.getRefreshObservable().subscribe(() => {
      this.loadArticles(); 
    });
  }

  loadArticles() {
    this.apiService.getAllArticles().subscribe(
      data => {
        this.articles = data;
        console.log(this.articles);
        this.allArticles = this.articles;
        this.cdr.detectChanges();
        
      },
      error => {
        console.error("Error fetching data:", error);
      }
    );
  }

  byDay(): void {
    const today = new Date();

    this.articles = this.allArticles.filter(article => {
      const articleDate = new Date(article.date);
      return articleDate.toDateString() === today.toDateString();
    });
    console.log(this.articles);
    this.cdr.detectChanges(); // Manually trigger change detection
  }

  byWeek(): void {
    const today = new Date();
    const startOfWeekDate = startOfWeek(today, { weekStartsOn: 1 }); // Monday
    const endOfWeekDate = endOfWeek(today, { weekStartsOn: 1 }); // Sunday

    this.articles = this.allArticles.filter(article => {
      const articleDate = new Date(article.date);
      return articleDate >= startOfWeekDate && articleDate <= endOfWeekDate;
    });

    console.log('byweek', this.articles);
    this.cdr.detectChanges();
  }
  

  byMonth(): void {
    const now = new Date();
    const currentMonth = now.getMonth(); // 0-based month (0 = January, 11 = December)
    const currentYear = now.getFullYear();

    this.articles = this.allArticles.filter(article => {
      const articleDate = new Date(article.date);  // Convert string date to Date object
      return articleDate.getMonth() === currentMonth && articleDate.getFullYear() === currentYear;
    });

    console.log('bymonth', this.articles);
    this.cdr.detectChanges(); // Trigger change detection if needed
  }
  
  searchArticles(): void {
    if (this.searchQuery.trim()) {
      console.log(this.searchQuery);
      this.apiService.searchPartners(this.searchQuery).subscribe(data => {
        this.articles = data;
      });
    } else {
      this.loadArticles();  // Reload all partners if search query is empty
    }
  }

  onSearchChange(event: any): void {
    console.log(event);
    this.searchQuery = event.target.value;
    this.searchArticles();
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
