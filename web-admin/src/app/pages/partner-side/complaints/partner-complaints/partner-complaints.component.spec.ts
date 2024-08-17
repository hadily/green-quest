import { ComponentFixture, TestBed } from '@angular/core/testing';

import { PartnerComplaintsComponent } from './partner-complaints.component';

describe('PartnerComplaintsComponent', () => {
  let component: PartnerComplaintsComponent;
  let fixture: ComponentFixture<PartnerComplaintsComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [PartnerComplaintsComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(PartnerComplaintsComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
