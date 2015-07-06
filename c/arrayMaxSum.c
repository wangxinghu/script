#include <stdio.h>
#include <stdlib.h>
int MaxSum(int array[], unsigned int len) {
    if(NULL == array || len <=0){
        return 0;
    }

    int curSum = 0, maxSum = 0, temp_start = 0;
    int index_start = 0, index_end = 0;     // 初始化子数组最大和下标
    int i = 0;
    for(i=0; i<len; i++){
        curSum += array[i];     // 累加

        if(curSum < 0){         // 当前和小于0，重置为0
            curSum = 0;
            temp_start = i+1;      // 记录子数组最大和的开始下标
        }

        if(curSum > maxSum){        // 当前和大于最大和，则重置最大和
            maxSum = curSum; 
            index_end = i;          // 调整子数组最大和的结束下标
            index_start = temp_start;      // 调整子数组最大和的开始下标
        }
    }

    if(maxSum == 0){            // 最大和依然为0，说明数组中所有元素都为负值
        maxSum = array[0];
        index_start = index_end = 0;                // 初始化子数组最大和下标
        for(i=1; i<len; i++){
            if(array[i] > maxSum){
                maxSum = array[i];
                index_start = index_end = i;        // 调整子数组最大和下标
            }
        }
    }

    // 输出最大和的子数组及其开始、结束下标
    printf("index_start: %d\nindex_end: %d\n", index_start, index_end);
    for(i=index_start; i<=index_end; i++){
        printf("%d\t", array[i]);
    }

    printf("\n\nmaxSum: %d", maxSum);
    return 0;
}

int main() {
    int a[] = {0,-2,5,6,-9,2,-3,7,-1,-10};
    MaxSum(a, 10);
}
