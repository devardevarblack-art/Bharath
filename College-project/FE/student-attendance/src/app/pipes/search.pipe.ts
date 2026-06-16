import { Pipe, PipeTransform } from '@angular/core';

@Pipe({
name: 'search'
})
export class SearchPipe implements PipeTransform {
  transform(items: any[], searchText: string): any[] {
    if (!items) return [];
    if (!searchText) return items;

    searchText = searchText.toLowerCase().replaceAll(' ','');
    
    return items.filter(item =>
      item.student_name?.toLowerCase().replaceAll(' ','').includes(searchText) ||
      item.dept_name?.toLowerCase().replaceAll(' ','').includes(searchText) ||
      item.student_id?.toString().includes(searchText)||
      item.teacher_name?.toLowerCase().replaceAll(' ','').includes(searchText)||
      item.teacher_id?.toString().includes(searchText)||
      item.dept_name?.toLowerCase().replaceAll(' ','').includes(searchText)
    );
  }
}
