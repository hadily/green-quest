import { Component, Inject } from '@angular/core';
import { MAT_DIALOG_DATA, MatDialogRef } from '@angular/material/dialog';
import { ApiService } from 'src/app/services/api.service';
import { RefreshService } from 'src/app/services/refresh.service';

@Component({
  selector: 'app-update-article',
  templateUrl: './update-article.component.html',
  styleUrl: './update-article.component.scss'
})
export class UpdateArticleComponent {
  article = {
    title: '',
    subTitle: '',
    summary: '',
    text: '',
    date: ''
  };

  constructor(
    public dialogRef: MatDialogRef<UpdateArticleComponent>,
      @Inject(MAT_DIALOG_DATA) public data: { articleId: number },
      private apiService: ApiService,
      private refreshService: RefreshService
    ) {
      console.log('Dialog data:', data);
    }

  ngOnInit(): void {
      this.loadArticleData();
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
    this.apiService.updateArticle(this.data.articleId, this.article).subscribe(
      response => {
        this.dialogRef.close(true);
        this.refreshService.triggerRefresh('/blog/articles'); // Notify other components
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
