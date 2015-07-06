local a = 1.13*100
local b,c = math.modf(a)
if 1-c < 0.0000001 then b = b+1 end
print(b)
