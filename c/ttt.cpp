#include <iostream>
#include <set>
#include <vector>
struct TileParam
{
    bool m_bIsUnlock;
    int m_nUnlockCondition;
    int m_nStoryID;
    int m_nPosX;
    int m_nPosY;
    int m_nSx;
    int m_nSy;

    TileParam()
    {
        m_bIsUnlock =false;
        m_nUnlockCondition =0;
        m_nStoryID =0;
        m_nPosX =0;
        m_nPosY =0;
        m_nSx =0;
        m_nSy =0;
    }

};

int main() {
    TileParam m_arrTileParams[9];
    m_arrTileParams[0].m_bIsUnlock =true;
    m_arrTileParams[0].m_nPosX=100;
    m_arrTileParams[1].m_bIsUnlock =true;
    m_arrTileParams[1].m_nPosY=200;

    return 0;
}
