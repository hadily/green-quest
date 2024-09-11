import { ComponentFixture, TestBed } from '@angular/core/testing';

import { UpdateAdminArticleComponent } from './update-admin-article.component';

describe('UpdateAdminArticleComponent', () => {
  let component: UpdateAdminArticleComponent;
  let fixture: ComponentFixture<UpdateAdminArticleComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [UpdateAdminArticleComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(UpdateAdminArticleComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
