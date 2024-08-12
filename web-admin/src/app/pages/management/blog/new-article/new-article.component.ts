import { Component, OnInit } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { ApiService } from 'src/app/services/api.service';
import { RefreshService } from 'src/app/services/refresh.service';

@Component({
  selector: 'app-new-article',
  templateUrl: './new-article.component.html',
  styleUrl: './new-article.component.scss'
})
export class NewArticleComponent implements OnInit {
  article = {
    title: '',
    subTitle: '',
    summary: '',
    writerId: null,
    text: '',
  };
  users: any[] = [];

  constructor(
    private http: HttpClient,
    private apiService: ApiService,
    private refreshService: RefreshService
  ) {}

  ngOnInit(): void {
    this.loadUsers();
  }

  loadUsers(): void {
    this.apiService.getAllUsers().subscribe(
      data => this.users = data,
      error => console.error('Error fetching users:', error)
    );
  }

  onSubmit(): void {
    this.apiService.createArticle(this.article).subscribe(
      response => {
        console.log('Article created:', response);
        this.refreshService.triggerRefresh('/blog/new-article'); // Emit a value to notify other components
      },
      error => {
        console.error('Error creating article:', error);
        // Optionally show an error message to the user
      }
    );
  }

}
