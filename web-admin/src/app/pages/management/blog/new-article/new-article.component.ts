import { Component, OnInit } from '@angular/core';
import { ApiService } from 'src/app/services/api.service';
import { Router } from '@angular/router';
import { AuthService } from 'src/app/modules/auth';
import { RefreshService } from 'src/app/services/refresh.service';
import { MatDialogRef } from '@angular/material/dialog';

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
    writerId: 1,
    text: '',
    status: 'Approved',
    review: ' none ',
    imageFilename: null,
  };
  file: any;

  constructor(
    private apiService: ApiService,
    private router: Router,
    private authService: AuthService,
    public dialogRef: MatDialogRef<NewArticleComponent>,
    private refreshService: RefreshService,
  ) {}

  ngOnInit(): void {}

  selectImage(event: any) {
    this.file = event.target.files[0];
    this.article.imageFilename =this.file;
    let reader = new FileReader();
    reader.onload = function () {
      let output: any = document.getElementById('imageFilename');
      output.src = reader.result;
    }
    reader.readAsDataURL(this.file);
  }

  onSubmit(): void {
    this.article.writerId = this.authService.currentUserValue?.id ?? 1;

    this.apiService.createArticle(this.article).subscribe(
      response => {
        console.log('Article created:', response);
        this.closeModal();
        this.refreshService.triggerRefresh('/blog/admin-articles');
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
