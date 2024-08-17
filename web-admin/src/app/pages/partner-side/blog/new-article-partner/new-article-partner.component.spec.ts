import { ComponentFixture, TestBed } from '@angular/core/testing';

import { NewArticlePartnerComponent } from './new-article-partner.component';

describe('NewArticlePartnerComponent', () => {
  let component: NewArticlePartnerComponent;
  let fixture: ComponentFixture<NewArticlePartnerComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [NewArticlePartnerComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(NewArticlePartnerComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
