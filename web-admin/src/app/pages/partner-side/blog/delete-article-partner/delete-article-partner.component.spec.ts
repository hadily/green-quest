import { ComponentFixture, TestBed } from '@angular/core/testing';

import { DeleteArticlePartnerComponent } from './delete-article-partner.component';

describe('DeleteArticlePartnerComponent', () => {
  let component: DeleteArticlePartnerComponent;
  let fixture: ComponentFixture<DeleteArticlePartnerComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [DeleteArticlePartnerComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(DeleteArticlePartnerComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
