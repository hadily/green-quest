import { HttpClient } from '@angular/common/http';
import { Component } from '@angular/core';
import { Router } from '@angular/router';
import { AuthService } from 'src/app/modules/auth';
import { ApiService } from 'src/app/services/api.service';
import { RefreshService } from 'src/app/services/refresh.service';

@Component({
  selector: 'app-new-article-partner',
  templateUrl: './new-article-partner.component.html',
  styleUrl: './new-article-partner.component.scss'
})
export class NewArticlePartnerComponent {

  article = {
    title: '',
    subTitle: '',
    summary: '',
    writerId: 0,
    text: '',
    imageFilename: null
  };
  users: any[] = [];
  file: any;

  constructor(
    private http: HttpClient,
    private apiService: ApiService,
    private refreshService: RefreshService,
    private router: Router,
    private authService: AuthService
  ) {}

  ngOnInit(): void {}

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
    const userId = this.authService.currentUserValue?.id ?? 0;
    this.article.writerId = userId;
    console.log(this.article);
    this.apiService.createArticle(this.article, this.file).subscribe(
      response => {
        console.log('Article created:', response);
        this.router.navigate(['/partner/blog/articles']); // Emit a value to notify other components
      },
      error => {
        console.error('Error creating article:', error);
        // Optionally show an error message to the user
      }
    );
  }

}
