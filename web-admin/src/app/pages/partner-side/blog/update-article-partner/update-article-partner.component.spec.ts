import { ComponentFixture, TestBed } from '@angular/core/testing';

import { UpdateArticlePartnerComponent } from './update-article-partner.component';

describe('UpdateArticlePartnerComponent', () => {
  let component: UpdateArticlePartnerComponent;
  let fixture: ComponentFixture<UpdateArticlePartnerComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [UpdateArticlePartnerComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(UpdateArticlePartnerComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
