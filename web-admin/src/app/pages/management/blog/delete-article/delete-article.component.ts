import { Component, Inject, OnInit } from '@angular/core';
import { MAT_DIALOG_DATA, MatDialogRef } from '@angular/material/dialog';
import { ApiService } from 'src/app/services/api.service';
import { RefreshService } from 'src/app/services/refresh.service';


@Component({
  selector: 'app-delete-article',
  templateUrl: './delete-article.component.html',
  styleUrl: './delete-article.component.scss'
})


export class DeleteArticleComponent {
  constructor(
    public dialogRef: MatDialogRef<DeleteArticleComponent>,
    @Inject(MAT_DIALOG_DATA) public data: { id: number },
    private apiService: ApiService,
    private refreshService: RefreshService
  ) {}

  ngOnInit(): void {
  }

  onDelete(): void {
    this.apiService.deleteArticle(this.data.id).subscribe(
      response => {
        console.log('Article deleted:', response);
        this.dialogRef.close(true); 
        this.refreshService.triggerRefresh('/blog/articles'); // Navigate to the partner list page

      },
      error => {
        console.error('Error deleting article:', error);
      }
    );
  }

  closeModal(): void {
    this.dialogRef.close(); 
  }
}
