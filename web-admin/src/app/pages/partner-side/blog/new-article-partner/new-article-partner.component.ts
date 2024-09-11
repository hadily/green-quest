import { Component } from '@angular/core';
import { MatDialogRef } from '@angular/material/dialog';
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
    status: 'pending',
    review: 'none',
    imageFilename: null
  };

  constructor(
    private apiService: ApiService,
    private refreshService: RefreshService,
    private authService: AuthService,
    public dialogRef: MatDialogRef<NewArticlePartnerComponent>,
  ) {}

  ngOnInit(): void {}

  selectImage(event: any) {
    this.article.imageFilename = event.target.files[0];
  }

  onSubmit(): void {
    this.article.writerId = this.authService.currentUserValue?.id ?? 1;
    console.log(this.article);
    this.apiService.createArticle(this.article).subscribe(
      response => {
        console.log('Article created:', response);
        this.closeModal();
        this.refreshService.triggerRefresh('/partner/blog/articles');
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
