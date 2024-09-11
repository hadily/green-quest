import { Component, Inject, OnInit } from '@angular/core';
import { MAT_DIALOG_DATA, MatDialogRef } from '@angular/material/dialog';
import { AuthService } from 'src/app/modules/auth';
import { ApiService } from 'src/app/services/api.service';
import { RefreshService } from 'src/app/services/refresh.service';
import { environment } from 'src/environments/environment';

@Component({
  selector: 'app-update-admin-article',
  templateUrl: './update-admin-article.component.html',
  styleUrl: './update-admin-article.component.scss'
})
export class UpdateAdminArticleComponent implements OnInit{

  article = {
    title: '',
    subTitle: '',
    summary: '',
    text: '',
    date: '',
    status: 'Approved',
    review: '',
    imageFilename: null
  };
  file: any;
  fileUrl = environment.fileUrl;


  constructor(
    public dialogRef: MatDialogRef<UpdateAdminArticleComponent>,
      @Inject(MAT_DIALOG_DATA) public data: { articleId: number },
      private apiService: ApiService,
      private refreshService: RefreshService,
    ) {
      console.log('Dialog data:', data);
    }

  ngOnInit(): void {
      this.loadArticleData();
  }

  selectImage(event: any) {
      this.article.imageFilename = event.target.files[0];
  }

  loadArticleData(): void {
    this.apiService.getArticleById(this.data.articleId).subscribe(
      response => {
        console.log('onsode apiService arrow fun', this.data.articleId);
        console.log('Loaded article data:', response); // Debugging log
        this.article = response;
      },
      error => {
        console.error('Error loading partner data:', error);
      }
    );
  }

  onUpdate(): void {
    this.file = this.article.imageFilename;
    this.article.status = 'Approved';
    console.log("before this.apiService.updateArticle call");
    this.apiService.updateArticle(this.data.articleId, this.article).subscribe(
      response => {
        console.log("article data ", this.article);
        console.log('Article updated:', response);
        this.refreshService.triggerRefresh('/blog/admin-articles');
        this.closeModal();
      },
      error => {
        console.error('Error updating article:', error);
        // Optionally show an error message to the user
      }
    );
  }

  closeModal(): void {
    this.dialogRef.close(); // Close the modal without any action
  }

}
