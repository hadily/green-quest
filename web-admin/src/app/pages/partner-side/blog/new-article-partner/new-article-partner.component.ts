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
  file: any;
  dialogRef: any;

  constructor(
    private http: HttpClient,
    private apiService: ApiService,
    private refreshService: RefreshService,
    private router: Router,
    private authService: AuthService
  ) {}

  ngOnInit(): void {}

  selectImage(event: any) {
    // this.article.imageFilename = event.target.files[0].name;
    // console.log(this.article.imageFilename);
    // let reader = new FileReader();
    // reader.onload = function () {
    //   let output: any = document.getElementById('imageFilename');
    //   output.src = reader.result;
    // }
    // reader.readAsDataURL(this.file);

    // this.file = event.target.files[0]; // works only the first time
    this.file = event.target.files[0].name;
  }

  onSubmit(): void {
    const userId = this.authService.currentUserValue?.id ?? 1;
    this.article.writerId = userId;
    
    const formData: FormData = new FormData();
    formData.append('title', this.article.title);
    formData.append('subTitle', this.article.subTitle);
    formData.append('summary', this.article.summary);
    formData.append('text', this.article.text);
    if (this.file) {
      formData.append('imageFilename', this.file, this.file.name);
    }
    
    this.apiService.createArticle(formData).subscribe(
      response => {
        console.log('Article created:', response);
        this.refreshService.triggerRefresh('/partner/blog/articles');
        this.closeModal();
      },
      error => {
        console.error('Error creating article:', error);
      }
    );
  }

  closeModal(): void {
    this.dialogRef.close();
  }

}
