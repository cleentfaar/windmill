using System;

namespace Windmill.Game
{
    public class Id
    {
        private Guid id;
        
        public Id(Guid id2)
        {
            id = id2;
        }

        public static Id Generate()
        {
            return new Id(System.Guid.NewGuid());
        }

        public override string ToString()
        {
            return id.ToString();
        }

        public static Id FromString(string s)
        {
            return new Id(Guid.Parse(s));
        }
    }
}