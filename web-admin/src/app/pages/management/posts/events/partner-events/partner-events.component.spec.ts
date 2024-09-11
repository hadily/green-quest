import { ComponentFixture, TestBed } from '@angular/core/testing';

import { PartnerEventsComponent } from './partner-events.component';

describe('PartnerEventsComponent', () => {
  let component: PartnerEventsComponent;
  let fixture: ComponentFixture<PartnerEventsComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [PartnerEventsComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(PartnerEventsComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
