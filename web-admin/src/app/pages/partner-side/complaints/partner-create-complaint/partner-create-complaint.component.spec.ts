import { ComponentFixture, TestBed } from '@angular/core/testing';

import { PartnerCreateComplaintComponent } from './partner-create-complaint.component';

describe('PartnerCreateComplaintComponent', () => {
  let component: PartnerCreateComplaintComponent;
  let fixture: ComponentFixture<PartnerCreateComplaintComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [PartnerCreateComplaintComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(PartnerCreateComplaintComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
