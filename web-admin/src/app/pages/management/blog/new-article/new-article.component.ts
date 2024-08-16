import { Component, OnInit } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { ApiService } from 'src/app/services/api.service';
import { RefreshService } from 'src/app/services/refresh.service';
import { Router } from '@angular/router';

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
  file: any;

  constructor(
    private http: HttpClient,
    private apiService: ApiService,
    private refreshService: RefreshService,
    private router: Router
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

  selectImage(event: any) {
    this.file = event.target.files[0];
    let reader = new FileReader();
    reader.onload = function () {
      let output: any = document.getElementById('imageFilename');
      output.src = reader.result;
    }
    reader.readAsDataURL(this.file);
  }

  onSubmit(): void {
    this.apiService.createArticle(this.article, this.file).subscribe(
      response => {
        console.log('Article created:', response);
        this.router.navigate(['/blog/articles']); // Emit a value to notify other components
      },
      error => {
        console.error('Error creating article:', error);
        // Optionally show an error message to the user
      }
    );
  }

}
