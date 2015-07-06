#include <stdio.h>
void quickSortPro(int a[], int min, int max) {
	if (min >= max) {
		return;
	}
	int i = min;
	int j = max;

    int temp = a[j];
    while (i < j) {
        while (a[i] <= temp && i < j) i++;
        a[j] = a[i];
        //i < j && j--;
        while (a[j] >= temp && i < j) j--;
        a[i] = a[j];
        //i < j && i++;
    }
    a[i] = temp;
    quickSortPro(a, min, i-1);
    quickSortPro(a, i+1, max);
}

void quickSort(int a[], int len) {
    quickSortPro(a, 0, len-1);
}
int main() {
    int a[] = {1,3,5,6,4,2,8,9,7};
    int len = sizeof(a)/sizeof(int);
    quickSort(a, len);
    for (int i=0; i < len; i++) {
        printf("%d ", a[i]);
    }
    printf("\n");
    return 0;
}

