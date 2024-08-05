import { Component, OnInit } from '@angular/core';
import { ApiService } from 'src/app/api.service';
import { ChangeDetectorRef } from '@angular/core';


@Component({
  selector: 'app-view-partners',
  templateUrl: './view-partners.component.html',
  styleUrls: ['./view-partners.component.scss']  // Correct property name
})
export class ViewPartnersComponent implements OnInit {
  partners: any[] = [];

  constructor(private apiService: ApiService, private cdr: ChangeDetectorRef) { }  // Use camelCase for service

  ngOnInit(): void {
    this.apiService.getAllPartners().subscribe(
      data => {
        this.partners = data;
        this.cdr.detectChanges();  // Manually trigger change detection
        console.log(this.partners);
      },
      error => {
        console.log("Error fetching data:", error);
      }
    );
  }
}
