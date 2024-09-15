import { ComponentFixture, TestBed } from '@angular/core/testing';

import { BookingsProdComponent } from './bookings-prod.component';

describe('BookingsProdComponent', () => {
  let component: BookingsProdComponent;
  let fixture: ComponentFixture<BookingsProdComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [BookingsProdComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(BookingsProdComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
