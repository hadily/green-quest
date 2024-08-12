import { Component, OnInit } from '@angular/core';
import { ApiService } from "../../services/api.service";
import { ActivatedRoute } from '@angular/router';

@Component({
  selector: 'app-account',
  templateUrl: './account.component.html',
})

export class AccountComponent implements OnInit {
  user: any;

  constructor(
    private apiService: ApiService,
    private route: ActivatedRoute,
  ) {}

  ngOnInit(): void {
    this.apiService.getCurrentUser().subscribe(
      (data) => {
        console.log(data);
        this.user = data;
      },
      (error) => {
        console.error('Error fetching user data', error);
      }
    );

  }
}
