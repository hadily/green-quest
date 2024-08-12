import { ComponentFixture, TestBed } from '@angular/core/testing';

import { UpdateComplaintsComponent } from './update-complaints.component';

describe('UpdateComplaintsComponent', () => {
  let component: UpdateComplaintsComponent;
  let fixture: ComponentFixture<UpdateComplaintsComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [UpdateComplaintsComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(UpdateComplaintsComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
