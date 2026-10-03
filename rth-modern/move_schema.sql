DO $$
DECLARE
    r RECORD;
BEGIN
    FOR r IN (SELECT tablename FROM pg_tables WHERE schemaname = 'rth') LOOP
        EXECUTE 'ALTER TABLE rth.' || quote_ident(r.tablename) || ' SET SCHEMA public';
    END LOOP;
END $$;
